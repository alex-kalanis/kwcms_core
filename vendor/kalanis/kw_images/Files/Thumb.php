<?php

namespace kalanis\kw_images\Files;


use kalanis\kw_files\FilesException;
use kalanis\kw_paths\Stuff;


/**
 * Class Thumb
 * File thumbnail
 * @package kalanis\kw_images\Files
 */
class Thumb extends AFiles
{
    /**
     * @param string $path
     * @throws FilesException
     * @return string|resource
     */
    public function get(string $path)
    {
        return $this->libProcessor->getFileProcessor()->readFile($this->getPath($path));
    }

    /**
     * @param string $path
     * @param string|resource $content
     * @throws FilesException
     * @return bool
     */
    public function set(string $path, $content): bool
    {
        return $this->libProcessor->getFileProcessor()->saveFile($this->getPath($path), $content);
    }

    /**
     * @param string $fileName
     * @param string $sourceDir
     * @param string $targetDir
     * @param bool $overwrite
     * @throws FilesException
     * @return bool
     */
    public function copy(string $fileName, string $sourceDir, string $targetDir, bool $overwrite = false): bool
    {
        return $this->dataCopy(
            Stuff::pathToArray($sourceDir) + [$this->config->getThumbDir(), $fileName],
            Stuff::pathToArray($targetDir) + [$this->config->getThumbDir(), $fileName],
            $overwrite,
            $this->getLang()->imThumbCannotFind(),
            $this->getLang()->imThumbAlreadyExistsHere(),
            $this->getLang()->imThumbCannotRemoveOld(),
            $this->getLang()->imThumbCannotCopyBase()
        );
    }

    /**
     * @param string $fileName
     * @param string $sourceDir
     * @param string $targetDir
     * @param bool $overwrite
     * @throws FilesException
     * @return bool
     */
    public function move(string $fileName, string $sourceDir, string $targetDir, bool $overwrite = false): bool
    {
        return $this->dataRename(
            Stuff::pathToArray($sourceDir) + [$this->config->getThumbDir(), $fileName],
            Stuff::pathToArray($targetDir) + [$this->config->getThumbDir(), $fileName],
            $overwrite,
            $this->getLang()->imThumbCannotFind(),
            $this->getLang()->imThumbAlreadyExistsHere(),
            $this->getLang()->imThumbCannotRemoveOld(),
            $this->getLang()->imThumbCannotMoveBase()
        );
    }

    /**
     * @param string $path
     * @param string $sourceName
     * @param string $targetName
     * @param bool $overwrite
     * @throws FilesException
     * @return bool
     */
    public function rename(string $path, string $sourceName, string $targetName, bool $overwrite = false): bool
    {
        return $this->dataRename(
            Stuff::pathToArray($path) + [$this->config->getThumbDir(), $sourceName],
            Stuff::pathToArray($path) + [$this->config->getThumbDir(), $targetName],
            $overwrite,
            $this->getLang()->imThumbCannotFind(),
            $this->getLang()->imThumbAlreadyExistsHere(),
            $this->getLang()->imThumbCannotRemoveOld(),
            $this->getLang()->imThumbCannotRenameBase()
        );
    }

    /**
     * @param string $sourceDir
     * @param string $fileName
     * @throws FilesException
     * @return bool
     */
    public function delete(string $sourceDir, string $fileName): bool
    {
        return $this->dataRemove(
            $this->getPath($sourceDir . DIRECTORY_SEPARATOR . $fileName),
            $this->getLang()->imThumbCannotRemove()
        );
    }

    public function getPath(string $path): array
    {
        $filePath = Stuff::removeEndingSlash(Stuff::directory($path));
        $fileName = Stuff::filename($path);
        return Stuff::pathToArray($filePath) + [$this->config->getThumbDir(), $fileName];
    }
}
