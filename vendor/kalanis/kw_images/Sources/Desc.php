<?php

namespace kalanis\kw_images\Sources;


use kalanis\kw_files\FilesException;
use kalanis\kw_paths\Stuff;


/**
 * Class Desc
 * Content description
 * @package kalanis\kw_images\Sources
 */
class Desc extends AFiles
{
    /**
     * @param string $path
     * @param bool $errorOnFail
     * @throws FilesException
     * @return string|resource
     */
    public function get(string $path, bool $errorOnFail = false): string
    {
        try {
            return $this->libProcessor->getFileProcessor()->readFile($this->getPath($path));
        } catch (FilesException $ex) {
            if (!$errorOnFail) {
                return '';
            }
            throw $ex;
        }
    }

    /**
     * @param string $path
     * @param string $content
     * @throws FilesException
     * @return bool
     */
    public function set(string $path, string $content): bool
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
            Stuff::pathToArray(Stuff::removeEndingSlash($sourceDir)) + [$this->config->getDescDir(), $fileName . $this->config->getDescExt()],
            Stuff::pathToArray(Stuff::removeEndingSlash($targetDir)) + [$this->config->getDescDir(), $fileName . $this->config->getDescExt()],
            $overwrite,
            $this->getLang()->imDescCannotFind(),
            $this->getLang()->imDescAlreadyExistsHere(),
            $this->getLang()->imDescCannotRemoveOld(),
            $this->getLang()->imDescCannotCopyBase()
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
            Stuff::pathToArray(Stuff::removeEndingSlash($sourceDir)) + [$this->config->getDescDir(), $fileName . $this->config->getDescExt()],
            Stuff::pathToArray(Stuff::removeEndingSlash($targetDir)) + [$this->config->getDescDir(), $fileName . $this->config->getDescExt()],
            $overwrite,
            $this->getLang()->imDescCannotFind(),
            $this->getLang()->imDescAlreadyExistsHere(),
            $this->getLang()->imDescCannotRemoveOld(),
            $this->getLang()->imDescCannotMoveBase()
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
        $whatPath = Stuff::pathToArray(Stuff::removeEndingSlash($path));

        return $this->dataRename(
            $whatPath + [$this->config->getDescDir(), $sourceName . $this->config->getDescExt()],
            $whatPath + [$this->config->getDescDir(), $targetName . $this->config->getDescExt()],
            $overwrite,
            $this->getLang()->imDescCannotFind(),
            $this->getLang()->imDescAlreadyExistsHere(),
            $this->getLang()->imDescCannotRemoveOld(),
            $this->getLang()->imDescCannotRenameBase()
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
            $this->getLang()->imDescCannotRemove()
        );
    }

    public function getPath(string $path): array
    {
        $filePath = Stuff::removeEndingSlash(Stuff::directory($path));
        $fileName = Stuff::filename($path);
        return Stuff::pathToArray($filePath) + [
            $this->config->getDescDir(),
            $fileName . $this->config->getDescExt()
        ];
    }
}
