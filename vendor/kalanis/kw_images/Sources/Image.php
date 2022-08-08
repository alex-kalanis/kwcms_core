<?php

namespace kalanis\kw_images\Sources;


use kalanis\kw_files\FilesException;
use kalanis\kw_paths\Stuff;


/**
 * Class Image
 * Main image itself
 * @package kalanis\kw_images\Sources
 */
class Image extends AFiles
{
    /**
     * @param string $fileName
     * @param string $ext
     * @throws FilesException
     * @return string
     */
    public function findFreeName(string $fileName, string $ext): string
    {
        return $this->libProcessor->getFileProcessor()->findFreeName([$fileName], $ext);
    }

    /**
     * @param string $path
     * @param string $format
     * @throws FilesException
     * @return string|null
     */
    public function getCreated(string $path, string $format = 'Y-m-d H:i:s'): ?string
    {
        $created = $this->libProcessor->getNodeProcessor()->created($this->getPath($path));
        return (is_null($created)) ? null : date($format, $created);
    }

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
        $this->libProcessor->getFileProcessor()->saveFile($this->getPath($path), $content);
        return true;
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
            Stuff::pathToArray($sourceDir) + [$fileName],
            Stuff::pathToArray($targetDir) + [$fileName],
            $overwrite,
            $this->getLang()->imImageCannotFind(),
            $this->getLang()->imImageAlreadyExistsHere(),
            $this->getLang()->imImageCannotRemoveOld(),
            $this->getLang()->imImageCannotCopyBase()
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
            Stuff::pathToArray($sourceDir) + [$fileName],
            Stuff::pathToArray($targetDir) + [$fileName],
            $overwrite,
            $this->getLang()->imImageCannotFind(),
            $this->getLang()->imImageAlreadyExistsHere(),
            $this->getLang()->imImageCannotRemoveOld(),
            $this->getLang()->imImageCannotMoveBase()
        );
    }

    /**
     * @param string $path
     * @param string $targetName
     * @param string $sourceName
     * @param bool $overwrite
     * @throws FilesException
     * @return bool
     */
    public function rename(string $path, string $sourceName, string $targetName, bool $overwrite = false): bool
    {
        return $this->dataRename(
            Stuff::pathToArray($path) + [$sourceName],
            Stuff::pathToArray($path) + [$targetName],
            $overwrite,
            $this->getLang()->imImageCannotFind(),
            $this->getLang()->imImageAlreadyExistsHere(),
            $this->getLang()->imImageCannotRemoveOld(),
            $this->getLang()->imImageCannotRenameBase()
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
        $whatPath = $this->getPath($sourceDir . DIRECTORY_SEPARATOR . $fileName);
        return $this->dataRemove($whatPath, $this->getLang()->imImageCannotRemove());
    }

    public function getPath(string $path): array
    {
        return Stuff::pathToArray($path);
    }
}
