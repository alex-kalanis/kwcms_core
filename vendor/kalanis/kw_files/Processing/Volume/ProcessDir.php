<?php

namespace kalanis\kw_files\Processing\Volume;


use Error;
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
use SplFileObject;


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

    public function __construct(string $path = '', ?IFLTranslations $lang = null)
    {
        $this->lang = $lang ?? new Translations();
        $this->setPath($path);
    }

    public function createDir(array $entry, bool $deep = false): bool
    {
        $path = $this->fullPath($entry);
        try {
            return mkdir($path, 0777, $deep);
        } catch (Error $ex) {
            throw new FilesException($this->lang->flCannotCreateDir($path), $ex->getCode(), $ex);
        }
    }

    public function readDir(array $entry, bool $loadRecursive = false, bool $wantSize = false): array
    {
        $path = $this->fullPath($entry);
        try {
            $iter = $loadRecursive
                ? new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path))
                : new FilesystemIterator($path)
            ;
            return array_map(
                [$this, 'intoNode'],
                array_filter(
                    array_filter(
                        iterator_to_array($iter),
                        [$this, 'filterFilesAndDirsOnly']
                    ),
                    [$this, 'filterDots']
                )
            );
        } catch (Error $ex) {
            throw new FilesException($this->lang->flCannotReadDir($path), $ex->getCode(), $ex);
        }
    }

    public function filterFilesAndDirsOnly(SplFileObject $file): bool
    {
        return in_array($file->getType(), [ITypes::TYPE_DIR, ITypes::TYPE_FILE]);
    }

    public function filterDots(SplFileObject $file): bool
    {
        return !in_array($file->getFilename(), ['.', '..']);
    }

    public function intoNode(SplFileObject $file): Node
    {
        $node = new Node();
        return $node->setData(
            $this->expandName($file->getPath()),
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
        } catch (Error $ex) {
            throw new FilesException($this->lang->flCannotCopyDir($src, $dst), $ex->getCode(), $ex);
        }
    }

    public function moveDir(array $source, array $dest): bool
    {
        $src = $this->fullPath($source);
        $dst = $this->fullPath($dest);
        try {
            return @rename($src, $dst);
        } catch (Error $ex) {
            throw new FilesException($this->lang->flCannotMoveDir($src, $dst), $ex->getCode(), $ex);
        }
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
        } catch (Error $ex) {
            throw new FilesException($this->lang->flCannotRemoveDir($path), $ex->getCode(), $ex);
        }
    }

    protected function fullPath(array $path): string
    {
        return $this->getPath() . DIRECTORY_SEPARATOR . $this->compactName($path);
    }
}
