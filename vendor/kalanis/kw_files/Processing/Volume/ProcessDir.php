<?php

namespace kalanis\kw_files\Processing\Volume;


use FilesystemIterator;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\IFLTranslations;
use kalanis\kw_files\Interfaces\IProcessDirs;
use kalanis\kw_files\Interfaces\ITypes;
use kalanis\kw_files\Node;
use kalanis\kw_files\Processing\TPath;
use kalanis\kw_files\Processing\TPathTransform;
use kalanis\kw_files\Translations;
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
    use TPath;
    use TPathTransform;
    use TRemoveCycle;
    use TVolumeCopy;

    /** @var IFLTranslations */
    protected $lang = null;
    /** @var int */
    protected $sliceStartParts = 0;

    public function __construct(string $path = '', ?IFLTranslations $lang = null)
    {
        $this->lang = $lang ?? new Translations();
        $this->setPath($path);
    }

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
            throw new FilesException($this->lang->flCannotCreateDir($path), $ex->getCode(), $ex);
        }
        // @codeCoverageIgnoreEnd
    }

    public function readDir(array $entry, bool $loadRecursive = false, bool $wantSize = false): array
    {
        $path = $this->fullPath($entry);
        try {
            $iter = $loadRecursive
                ? new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path))
                : new FilesystemIterator($path, 0)
            ;
            return array_values(
                array_map(
                    [$this, 'intoNode'],
                    array_filter(
                        array_filter(
                            array_filter(
                                ($loadRecursive ? [] : [$this->addRootNode($path)]) + iterator_to_array($iter)
                            ),
                            [$this, 'filterFilesAndDirsOnly']
                        ),
                        [$this, 'filterDots']
                    )
                )
            );
            // @codeCoverageIgnoreStart
        } catch (Throwable $ex) {
            throw new FilesException($this->lang->flCannotReadDir($path), $ex->getCode(), $ex);
        }
        // @codeCoverageIgnoreEnd
    }

    protected function addRootNode(string $path): SplFileInfo
    {
        return new SplFileInfo($path);
    }

    public function filterFilesAndDirsOnly(SplFileInfo $file): bool
    {
        return in_array($file->getType(), [ITypes::TYPE_DIR, ITypes::TYPE_FILE]);
    }

    public function filterDots(SplFileInfo $file): bool
    {
        return '..' !== $file->getFilename();
    }

    public function intoNode(SplFileInfo $file): Node
    {
        $node = new Node();
        return $node->setData(
            array_slice($this->expandName($file->getRealPath()), $this->sliceStartParts),
            $file->getSize(),
            $file->getType()
        );
    }

    public function copyDir(array $source, array $dest): bool
    {
        $src = $this->fullPath($source);
        $dst = $this->fullPath($dest);
        try {
            return $this->xcopy($src, $dst);
            // @codeCoverageIgnoreStart
        } catch (Throwable $ex) {
            throw new FilesException($this->lang->flCannotCopyDir($src, $dst), $ex->getCode(), $ex);
        }
        // @codeCoverageIgnoreEnd
    }

    public function moveDir(array $source, array $dest): bool
    {
        $src = $this->fullPath($source);
        $dst = $this->fullPath($dest);
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
            throw new FilesException($this->lang->flCannotMoveDir($src, $dst), $ex->getCode(), $ex);
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
            throw new FilesException($this->lang->flCannotRemoveDir($path), $ex->getCode(), $ex);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param array<string> $path
     * @return string
     */
    protected function fullPath(array $path): string
    {
        return $this->getPath() . DIRECTORY_SEPARATOR . $this->compactName($path);
    }
}
