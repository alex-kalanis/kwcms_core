<?php

namespace kalanis\kw_files\Processing\Volume;


use Error;
use FilesystemIterator;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\IFLTranslations;
use kalanis\kw_files\Interfaces\IProcessDirs;
use kalanis\kw_files\Interfaces\ITypes;
use kalanis\kw_files\Node;
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
    use TPathTransform;
    use TRemoveCycle;
    use TVolumeCopy;

    /** @var IFLTranslations */
    protected $lang = null;

    public function __construct(?IFLTranslations $lang = null)
    {
        $this->lang = $lang ?? new Translations();
    }

    public function createDir(array $entry, bool $deep = false): bool
    {
        try {
            return mkdir($this->compactName($entry), 0777, $deep);
        } catch (Error $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function readDir(array $entry, bool $loadRecursive = false): array
    {
        try {
            $entry = $this->compactName($entry);
            $iter = $loadRecursive
                ? new RecursiveIteratorIterator(new RecursiveDirectoryIterator($entry))
                : new FilesystemIterator($entry)
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
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
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
        try {
            return $this->xcopy($this->compactName($source), $this->compactName($dest));
        } catch (Error $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function moveDir(array $source, array $dest): bool
    {
        try {
            return @rename($this->compactName($source), $this->compactName($dest));
        } catch (Error $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function deleteDir(array $entry, bool $deep = false): bool
    {
        if ($deep) {
            return $this->removeCycle($this->compactName($entry));
        } else {
            try {
                return @rmdir($this->compactName($entry));
            } catch (Error $ex) {
                throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
            }
        }
    }
}
