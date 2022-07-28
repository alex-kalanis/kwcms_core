<?php

namespace kalanis\kw_images\Files;


use kalanis\kw_files\FilesException;
use kalanis\kw_paths\Stuff;


/**
 * Class Desc
 * Content description
 * @package kalanis\kw_images\Files
 */
class Desc extends AFiles
{
    /**
     * @param string $path
     * @param bool $errorOnFail
     * @throws FilesException
     * @return string
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
     */
    public function set(string $path, string $content): void
    {
        $this->libProcessor->getFileProcessor()->saveFile($this->getPath($path), $content);
    }

    /**
     * @param string $fileName
     * @param string $sourceDir
     * @param string $targetDir
     * @param bool $overwrite
     * @throws FilesException
     */
    public function copy(string $fileName, string $sourceDir, string $targetDir, bool $overwrite = false): void
    {
        $this->dataCopy(
            Stuff::pathToArray(Stuff::removeEndingSlash($sourceDir)) + [$this->libProcessor->getConfig()->getDescDir(), $fileName . $this->libProcessor->getConfig()->getDescExt()],
            Stuff::pathToArray(Stuff::removeEndingSlash($targetDir)) + [$this->libProcessor->getConfig()->getDescDir(), $fileName . $this->libProcessor->getConfig()->getDescExt()],
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
     */
    public function move(string $fileName, string $sourceDir, string $targetDir, bool $overwrite = false): void
    {
        $this->dataRename(
            Stuff::pathToArray(Stuff::removeEndingSlash($sourceDir)) + [$this->libProcessor->getConfig()->getDescDir(), $fileName . $this->libProcessor->getConfig()->getDescExt()],
            Stuff::pathToArray(Stuff::removeEndingSlash($targetDir)) + [$this->libProcessor->getConfig()->getDescDir(), $fileName . $this->libProcessor->getConfig()->getDescExt()],
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
     */
    public function rename(string $path, string $sourceName, string $targetName, bool $overwrite = false): void
    {
        $whatPath = Stuff::pathToArray(Stuff::removeEndingSlash($path));

        $this->dataRename(
            $whatPath + [$this->libProcessor->getConfig()->getDescDir(), $sourceName . $this->libProcessor->getConfig()->getDescExt()],
            $whatPath + [$this->libProcessor->getConfig()->getDescDir(), $targetName . $this->libProcessor->getConfig()->getDescExt()],
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
     */
    public function delete(string $sourceDir, string $fileName): void
    {
        $whatPath = $this->getPath($sourceDir . DIRECTORY_SEPARATOR . $fileName);
        $this->dataRemove($whatPath, $this->getLang()->imDescCannotRemove());
    }

    public function getPath(string $path): array
    {
        $filePath = Stuff::removeEndingSlash(Stuff::directory($path));
        $fileName = Stuff::filename($path);
        return Stuff::pathToArray($filePath) + [
            $this->libProcessor->getConfig()->getDescDir(),
            $fileName . $this->libProcessor->getConfig()->getDescExt()
        ];
    }
}
