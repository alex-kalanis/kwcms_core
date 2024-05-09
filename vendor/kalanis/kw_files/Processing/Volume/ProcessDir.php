<?php

namespace kalanis\kw_files\Processing\Volume;


use FilesystemIterator;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\IFLTranslations;
use kalanis\kw_files\Interfaces\IProcessDirs;
use kalanis\kw_files\Interfaces\ITypes;
use kalanis\kw_files\Node;
use kalanis\kw_files\Processing\TPath;
use kalanis\kw_files\Traits\TLang;
use kalanis\kw_files\Traits\TSubPart;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\Extras\TPathTransform;
use kalanis\kw_paths\PathsException;
use kalanis\kw_storage\Extras\TRemoveCycle;
use kalanis\kw_storage\Extras\TVolumeCopy;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Throwable;


/**
 * Class ProcessDir
 * @package kalanis\kw_files\Processing\Volume
 * Process dirs in basic ways
 */
class ProcessDir implements IProcessDirs
{
    use TLang;
    use TPath;
    use TPathTransform;
    use TRemoveCycle;
    use TSubPart;
    use TVolumeCopy;

    protected int $sliceStartParts = 0;
    protected int $sliceCustomParts = 0;

    /**
     * @param string $path
     * @param IFLTranslations|null $lang
     * @throws PathsException
     */
    public function __construct(string $path = '', ?IFLTranslations $lang = null)
    {
        $this->setFlLang($lang);
        $this->setPath($path);
    }

    /**
     * @param string $path
     * @throws PathsException
     */
    public function setPath(string $path = ''): void
    {
        $used = realpath($path);
        if (false !== $used) {
            $this->path = $used;
            $this->sliceStartParts = count($this->expandName($used));
        }
    }

    public function createDir(array $entry, bool $deep = false): bool
    {
        $path = $this->fullPath($entry);
        try {
            return @mkdir($path, 0777, $deep);
            // @codeCoverageIgnoreStart
        } catch (Throwable $ex) {
            throw new FilesException($this->getFlLang()->flCannotCreateDir($path), $ex->getCode(), $ex);
        }
        // @codeCoverageIgnoreEnd
    }

    public function readDir(array $entry, bool $loadRecursive = false, bool $wantSize = false): array
    {
        $path = $this->fullPath($entry);
        $this->sliceCustomParts = count($entry);
        if (!is_dir($path)) {
            throw new FilesException($this->getFlLang()->flCannotReadDir($path));
        }

        try {
            $iter = $loadRecursive
                ? new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path))
                : new FilesystemIterator($path, 0)
            ;
            return array_values(
                array_merge(
                    $this->addRootNode($path, $loadRecursive),
                    array_map(
                        [$this, 'intoNode'],
                        array_filter(
                            array_filter(
                                array_filter(
                                    iterator_to_array($iter)
                                ),
                                [$this, 'filterFilesAndDirsOnly']
                            ),
                            [$this, 'filterDots']
                        )
                    )
                )
            );
            // @codeCoverageIgnoreStart
        } catch (Throwable $ex) {
            throw new FilesException($this->getFlLang()->flCannotReadDir($path), $ex->getCode(), $ex);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param string $path
     * @param bool $loadRecursive
     * @throws PathsException
     * @return Node[]
     */
    protected function addRootNode(string $path, bool $loadRecursive): array
    {
        if (PHP_VERSION_ID >= 80200) {
            // these versions pass root node in filesystem iterator implicitly
            return [];
        }
        if ($loadRecursive) {
            // recursive iterator pass root node implicitly
            return [];
        }
        // filesystem iterator in older versions skips root node
        return [$this->intoNode(new SplFileInfo($path))];
    }

    public function filterFilesAndDirsOnly(SplFileInfo $file): bool
    {
        return in_array($file->getType(), [ITypes::TYPE_DIR, ITypes::TYPE_FILE]);
    }

    public function filterDots(SplFileInfo $file): bool
    {
        return '..' !== $file->getFilename();
    }

    /**
     * @param SplFileInfo $file
     * @throws PathsException
     * @return Node
     */
    public function intoNode(SplFileInfo $file): Node
    {
        $node = new Node();
        return $node->setData(
            array_slice($this->expandName($file->getRealPath()), $this->sliceStartParts + $this->sliceCustomParts),
            $file->getSize(),
            $file->getType()
        );
    }

    public function copyDir(array $source, array $dest): bool
    {
        $src = $this->fullPath($source);
        $dst = $this->fullPath($dest);
        if ($this->isSubPart($dest, $source)) {
            return false;
        }

        if (!file_exists($src)) {
            return false;
        }

        $dstArr = new ArrayPath();
        $dstArr->setArray($dest);

        if (!file_exists($this->fullPath($dstArr->getArrayDirectory()))) {
            return false;
        }

        if (file_exists($dst)) {
            return false;
        }

        try {
            return $this->xcopy($src, $dst);
            // @codeCoverageIgnoreStart
        } catch (Throwable $ex) {
            throw new FilesException($this->getFlLang()->flCannotCopyDir($src, $dst), $ex->getCode(), $ex);
        }
        // @codeCoverageIgnoreEnd
    }

    public function moveDir(array $source, array $dest): bool
    {
        $src = $this->fullPath($source);
        $dst = $this->fullPath($dest);
        if ($this->isSubPart($dest, $source)) {
            return false;
        }

        if (!file_exists($src)) {
            return false;
        }

        $dstArr = new ArrayPath();
        $dstArr->setArray($dest);

        if (!file_exists($this->fullPath($dstArr->getArrayDirectory()))) {
            return false;
        }

        if (file_exists($dst)) {
            return false;
        }

        try {
            // original call with doomed sub-calls chmod and chown - see
            // @link https://www.php.net/manual/en/function.rename.php#117590
            // return @rename($src, $dst);

            // to avoid that internal bug...
            $v1 = $this->copyDir($source, $dest);
            $v2 = $this->deleteDir($source, true);
            return $v1 && $v2;
            // @codeCoverageIgnoreStart
        } catch (Throwable $ex) {
            throw new FilesException($this->getFlLang()->flCannotMoveDir($src, $dst), $ex->getCode(), $ex);
        }
        // @codeCoverageIgnoreEnd
    }

    public function deleteDir(array $entry, bool $deep = false): bool
    {
        $path = $this->fullPath($entry);
        try {
            if ($deep) {
                return $this->removeCycle($path);
            } else {
                return @rmdir($path);
            }
            // @codeCoverageIgnoreStart
        } catch (Throwable $ex) {
            throw new FilesException($this->getFlLang()->flCannotRemoveDir($path), $ex->getCode(), $ex);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param array<string> $path
     * @throws PathsException
     * @return string
     */
    protected function fullPath(array $path): string
    {
        return $this->getPath() . DIRECTORY_SEPARATOR . $this->compactName($path);
    }

    /**
     * @return string
     * @codeCoverageIgnore only when path fails
     */
    protected function noDirectoryDelimiterSet(): string
    {
        return $this->getFlLang()->flNoDirectoryDelimiterSet();
    }
}
