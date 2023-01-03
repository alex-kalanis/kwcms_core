<?php

namespace kalanis\kw_images\Sources;


use kalanis\kw_files\Extended\FindFreeName;
use kalanis\kw_files\FilesException;


/**
 * Class Image
 * Main image itself
 * @package kalanis\kw_images\Sources
 */
class Image extends AFiles
{
    /**
     * @param string[] $path
     * @param string $fileName
     * @param string $ext
     * @throws FilesException
     * @return string
     */
    public function findFreeName(array $path, string $fileName, string $ext): string
    {
        $libFinder = new FindFreeName($this->libNode);
        return $libFinder->findFreeName($path, $fileName, $ext);
    }

    /**
     * @param string[] $path
     * @param string $format
     * @throws FilesException
     * @return string|null
     */
    public function getCreated(array $path, string $format = 'Y-m-d H:i:s'): ?string
    {
        $created = $this->libNode->created($this->getPath($path));
        return (is_null($created)) ? null : date($format, $created);
    }

    /**
     * @param string[] $path
     * @throws FilesException
     * @return string|resource
     */
    public function get(array $path)
    {
        return $this->libFile->readFile($this->getPath($path));
    }

    /**
     * @param string[] $path
     * @param string|resource $content
     * @throws FilesException
     * @return bool
     */
    public function set(array $path, $content): bool
    {
        $this->libFile->saveFile($this->getPath($path), $content);
        return true;
    }

    /**
     * @param string $fileName
     * @param string[] $sourceDir
     * @param string[] $targetDir
     * @param bool $overwrite
     * @throws FilesException
     * @return bool
     */
    public function copy(string $fileName, array $sourceDir, array $targetDir, bool $overwrite = false): bool
    {
        return $this->dataCopy(
            array_merge($sourceDir, [$fileName]),
            array_merge($targetDir, [$fileName]),
            $overwrite,
            $this->getLang()->imImageCannotFind(),
            $this->getLang()->imImageAlreadyExistsHere(),
            $this->getLang()->imImageCannotRemoveOld(),
            $this->getLang()->imImageCannotCopyBase()
        );
    }

    /**
     * @param string $fileName
     * @param string[] $sourceDir
     * @param string[] $targetDir
     * @param bool $overwrite
     * @throws FilesException
     * @return bool
     */
    public function move(string $fileName, array $sourceDir, array $targetDir, bool $overwrite = false): bool
    {
        return $this->dataRename(
            array_merge($sourceDir, [$fileName]),
            array_merge($targetDir, [$fileName]),
            $overwrite,
            $this->getLang()->imImageCannotFind(),
            $this->getLang()->imImageAlreadyExistsHere(),
            $this->getLang()->imImageCannotRemoveOld(),
            $this->getLang()->imImageCannotMoveBase()
        );
    }

    /**
     * @param string[] $path
     * @param string $targetName
     * @param string $sourceName
     * @param bool $overwrite
     * @throws FilesException
     * @return bool
     */
    public function rename(array $path, string $sourceName, string $targetName, bool $overwrite = false): bool
    {
        return $this->dataRename(
            array_merge($path, [$sourceName]),
            array_merge($path, [$targetName]),
            $overwrite,
            $this->getLang()->imImageCannotFind(),
            $this->getLang()->imImageAlreadyExistsHere(),
            $this->getLang()->imImageCannotRemoveOld(),
            $this->getLang()->imImageCannotRenameBase()
        );
    }

    /**
     * @param string[] $sourceDir
     * @param string $fileName
     * @throws FilesException
     * @return bool
     */
    public function delete(array $sourceDir, string $fileName): bool
    {
        $whatPath = $this->getPath(array_merge($sourceDir, [$fileName]));
        return $this->dataRemove($whatPath, $this->getLang()->imImageCannotRemove());
    }

    public function getPath(array $path): array
    {
        return $path;
    }
}
