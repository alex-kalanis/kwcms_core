<?php

namespace kalanis\kw_images\Sources;


use kalanis\kw_files\FilesException;


/**
 * Class Desc
 * Content description
 * @package kalanis\kw_images\Sources
 */
class Desc extends AFiles
{
    /**
     * @param string[] $path
     * @param bool $errorOnFail
     * @throws FilesException
     * @return string
     */
    public function get(array $path, bool $errorOnFail = false): string
    {
        try {
            $content = $this->libFile->readFile($this->getPath($path));
            return is_resource($content) ? strval(stream_get_contents($content, 0, -1)) : strval($content);
        } catch (FilesException $ex) {
            if (!$errorOnFail) {
                return '';
            }
            throw $ex;
        }
    }

    /**
     * @param string[] $path
     * @param string $content
     * @throws FilesException
     * @return bool
     */
    public function set(array $path, string $content): bool
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
            array_merge($sourceDir, [$this->config->getDescDir(), $fileName . $this->config->getDescExt()]),
            array_merge($targetDir, [$this->config->getDescDir(), $fileName . $this->config->getDescExt()]),
            $overwrite,
            $this->getLang()->imDescCannotFind(),
            $this->getLang()->imDescAlreadyExistsHere(),
            $this->getLang()->imDescCannotRemoveOld(),
            $this->getLang()->imDescCannotCopyBase()
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
            array_merge($sourceDir, [$this->config->getDescDir(), $fileName . $this->config->getDescExt()]),
            array_merge($targetDir, [$this->config->getDescDir(), $fileName . $this->config->getDescExt()]),
            $overwrite,
            $this->getLang()->imDescCannotFind(),
            $this->getLang()->imDescAlreadyExistsHere(),
            $this->getLang()->imDescCannotRemoveOld(),
            $this->getLang()->imDescCannotMoveBase()
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
            array_merge($path, [$this->config->getDescDir(), $sourceName . $this->config->getDescExt()]),
            array_merge($path, [$this->config->getDescDir(), $targetName . $this->config->getDescExt()]),
            $overwrite,
            $this->getLang()->imDescCannotFind(),
            $this->getLang()->imDescAlreadyExistsHere(),
            $this->getLang()->imDescCannotRemoveOld(),
            $this->getLang()->imDescCannotRenameBase()
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
            array_merge($sourceDir, [$this->config->getDescDir(), $fileName . $this->config->getDescExt()]),
            $this->getLang()->imDescCannotRemove()
        );
    }

    public function getPath(array $path): array
    {
        $fileName = array_pop($path);
        return array_merge($path, [$this->config->getDescDir(), $fileName . $this->config->getDescExt()]);
    }
}
