<?php

namespace kalanis\kw_images\Sources;


use kalanis\kw_files\FilesException;


/**
 * Class Thumb
 * File thumbnail
 * @package kalanis\kw_images\Sources
 */
class Thumb extends AFiles
{
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
        return $this->libFile->saveFile($this->getPath($path), $content);
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
            array_merge($sourceDir, [$this->config->getThumbDir(), $fileName]),
            array_merge($targetDir, [$this->config->getThumbDir(), $fileName]),
            $overwrite,
            $this->getLang()->imThumbCannotFind(),
            $this->getLang()->imThumbAlreadyExistsHere(),
            $this->getLang()->imThumbCannotRemoveOld(),
            $this->getLang()->imThumbCannotCopyBase()
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
            array_merge($sourceDir, [$this->config->getThumbDir(), $fileName]),
            array_merge($targetDir, [$this->config->getThumbDir(), $fileName]),
            $overwrite,
            $this->getLang()->imThumbCannotFind(),
            $this->getLang()->imThumbAlreadyExistsHere(),
            $this->getLang()->imThumbCannotRemoveOld(),
            $this->getLang()->imThumbCannotMoveBase()
        );
    }

    /**
     * @param string[] $path
     * @param string $sourceName
     * @param string $targetName
     * @param bool $overwrite
     * @throws FilesException
     * @return bool
     */
    public function rename(array $path, string $sourceName, string $targetName, bool $overwrite = false): bool
    {
        return $this->dataRename(
            array_merge($path, [$this->config->getThumbDir(), $sourceName]),
            array_merge($path, [$this->config->getThumbDir(), $targetName]),
            $overwrite,
            $this->getLang()->imThumbCannotFind(),
            $this->getLang()->imThumbAlreadyExistsHere(),
            $this->getLang()->imThumbCannotRemoveOld(),
            $this->getLang()->imThumbCannotRenameBase()
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
        return $this->dataRemove(
            $this->getPath(array_merge($sourceDir, [$fileName])),
            $this->getLang()->imThumbCannotRemove()
        );
    }

    public function getPath(array $path): array
    {
        $fileName = strval(array_pop($path));
        return array_merge($path, [$this->config->getThumbDir(), $fileName]);
    }
}
