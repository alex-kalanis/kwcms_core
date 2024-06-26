<?php

namespace kalanis\kw_images\Sources;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Traits\TToString;
use kalanis\kw_paths\PathsException;


/**
 * Class Thumb
 * File thumbnail
 * @package kalanis\kw_images\Sources
 */
class Thumb extends AFiles
{
    use TToString;

    /**
     * @param string[] $path
     * @throws FilesException
     * @throws PathsException
     * @return string|resource
     */
    public function get(array $path)
    {
        return $this->lib->readFile($this->getPath($path));
    }

    /**
     * @param string[] $path
     * @param string|resource $content
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function set(array $path, $content): bool
    {
        return $this->lib->saveFile($this->getPath($path), $this->toString(implode(DIRECTORY_SEPARATOR, $path), $content));
    }

    /**
     * @param string $fileName
     * @param string[] $sourceDir
     * @param string[] $targetDir
     * @param bool $overwrite
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function copy(string $fileName, array $sourceDir, array $targetDir, bool $overwrite = false): bool
    {
        return $this->dataCopy(
            array_merge($sourceDir, [$this->config->getThumbDir(), $fileName]),
            array_merge($targetDir, [$this->config->getThumbDir(), $fileName]),
            $overwrite,
            $this->getImLang()->imThumbCannotFind(),
            $this->getImLang()->imThumbAlreadyExistsHere(),
            $this->getImLang()->imThumbCannotRemoveOld(),
            $this->getImLang()->imThumbCannotCopyBase()
        );
    }

    /**
     * @param string $fileName
     * @param string[] $sourceDir
     * @param string[] $targetDir
     * @param bool $overwrite
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function move(string $fileName, array $sourceDir, array $targetDir, bool $overwrite = false): bool
    {
        return $this->dataRename(
            array_merge($sourceDir, [$this->config->getThumbDir(), $fileName]),
            array_merge($targetDir, [$this->config->getThumbDir(), $fileName]),
            $overwrite,
            $this->getImLang()->imThumbCannotFind(),
            $this->getImLang()->imThumbAlreadyExistsHere(),
            $this->getImLang()->imThumbCannotRemoveOld(),
            $this->getImLang()->imThumbCannotMoveBase()
        );
    }

    /**
     * @param string[] $path
     * @param string $sourceName
     * @param string $targetName
     * @param bool $overwrite
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function rename(array $path, string $sourceName, string $targetName, bool $overwrite = false): bool
    {
        return $this->dataRename(
            array_merge($path, [$this->config->getThumbDir(), $sourceName]),
            array_merge($path, [$this->config->getThumbDir(), $targetName]),
            $overwrite,
            $this->getImLang()->imThumbCannotFind(),
            $this->getImLang()->imThumbAlreadyExistsHere(),
            $this->getImLang()->imThumbCannotRemoveOld(),
            $this->getImLang()->imThumbCannotRenameBase()
        );
    }

    /**
     * @param string[] $sourceDir
     * @param string $fileName
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function delete(array $sourceDir, string $fileName): bool
    {
        return $this->dataRemove(
            $this->getPath(array_merge($sourceDir, [$fileName])),
            $this->getImLang()->imThumbCannotRemove()
        );
    }

    public function getPath(array $path): array
    {
        $fileName = strval(array_pop($path));
        return array_merge($path, [$this->config->getThumbDir(), $fileName]);
    }
}
