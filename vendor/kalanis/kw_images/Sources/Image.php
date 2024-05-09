<?php

namespace kalanis\kw_images\Sources;


use kalanis\kw_files\Extended\FindFreeName;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Traits\TToString;
use kalanis\kw_paths\PathsException;


/**
 * Class Image
 * Main image itself
 * @package kalanis\kw_images\Sources
 */
class Image extends AFiles
{
    use TToString;

    /**
     * @param string[] $path
     * @param string $fileName
     * @param string $ext
     * @throws FilesException
     * @throws PathsException
     * @return string
     */
    public function findFreeName(array $path, string $fileName, string $ext): string
    {
        $libFinder = new FindFreeName($this->lib->getNode());
        return $libFinder->findFreeName($path, $fileName, $ext);
    }

    /**
     * @param string[] $path
     * @param string $format
     * @throws FilesException
     * @throws PathsException
     * @return string|null
     */
    public function getCreated(array $path, string $format = 'Y-m-d H:i:s'): ?string
    {
        $created = $this->lib->created($this->getPath($path));
        return (is_null($created)) ? null : date($format, $created);
    }

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
            array_merge($sourceDir, [$fileName]),
            array_merge($targetDir, [$fileName]),
            $overwrite,
            $this->getImLang()->imImageCannotFind(),
            $this->getImLang()->imImageAlreadyExistsHere(),
            $this->getImLang()->imImageCannotRemoveOld(),
            $this->getImLang()->imImageCannotCopyBase()
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
            array_merge($sourceDir, [$fileName]),
            array_merge($targetDir, [$fileName]),
            $overwrite,
            $this->getImLang()->imImageCannotFind(),
            $this->getImLang()->imImageAlreadyExistsHere(),
            $this->getImLang()->imImageCannotRemoveOld(),
            $this->getImLang()->imImageCannotMoveBase()
        );
    }

    /**
     * @param string[] $path
     * @param string $targetName
     * @param string $sourceName
     * @param bool $overwrite
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function rename(array $path, string $sourceName, string $targetName, bool $overwrite = false): bool
    {
        return $this->dataRename(
            array_merge($path, [$sourceName]),
            array_merge($path, [$targetName]),
            $overwrite,
            $this->getImLang()->imImageCannotFind(),
            $this->getImLang()->imImageAlreadyExistsHere(),
            $this->getImLang()->imImageCannotRemoveOld(),
            $this->getImLang()->imImageCannotRenameBase()
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
        $whatPath = $this->getPath(array_merge($sourceDir, [$fileName]));
        return $this->dataRemove($whatPath, $this->getImLang()->imImageCannotRemove());
    }

    public function getPath(array $path): array
    {
        return $path;
    }
}
