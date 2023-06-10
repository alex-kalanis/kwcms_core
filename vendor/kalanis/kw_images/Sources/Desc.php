<?php

namespace kalanis\kw_images\Sources;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Traits\TToString;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;


/**
 * Class Desc
 * Content description
 * @package kalanis\kw_images\Sources
 */
class Desc extends AFiles
{
    use TToString;

    /**
     * @param string[] $path
     * @param bool $errorOnFail
     * @throws FilesException
     * @throws PathsException
     * @return string
     */
    public function get(array $path, bool $errorOnFail = false): string
    {
        try {
            return $this->toString(Stuff::arrayToPath($path), $this->lib->readFile($this->getPath($path)));
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
     * @throws PathsException
     * @return bool
     */
    public function set(array $path, string $content): bool
    {
        return $this->lib->saveFile($this->getPath($path), $content);
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
            array_merge($sourceDir, [$this->config->getDescDir(), $fileName . $this->config->getDescExt()]),
            array_merge($targetDir, [$this->config->getDescDir(), $fileName . $this->config->getDescExt()]),
            $overwrite,
            $this->getImLang()->imDescCannotFind(),
            $this->getImLang()->imDescAlreadyExistsHere(),
            $this->getImLang()->imDescCannotRemoveOld(),
            $this->getImLang()->imDescCannotCopyBase()
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
            array_merge($sourceDir, [$this->config->getDescDir(), $fileName . $this->config->getDescExt()]),
            array_merge($targetDir, [$this->config->getDescDir(), $fileName . $this->config->getDescExt()]),
            $overwrite,
            $this->getImLang()->imDescCannotFind(),
            $this->getImLang()->imDescAlreadyExistsHere(),
            $this->getImLang()->imDescCannotRemoveOld(),
            $this->getImLang()->imDescCannotMoveBase()
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
            array_merge($path, [$this->config->getDescDir(), $sourceName . $this->config->getDescExt()]),
            array_merge($path, [$this->config->getDescDir(), $targetName . $this->config->getDescExt()]),
            $overwrite,
            $this->getImLang()->imDescCannotFind(),
            $this->getImLang()->imDescAlreadyExistsHere(),
            $this->getImLang()->imDescCannotRemoveOld(),
            $this->getImLang()->imDescCannotRenameBase()
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
            array_merge($sourceDir, [$this->config->getDescDir(), $fileName . $this->config->getDescExt()]),
            $this->getImLang()->imDescCannotRemove()
        );
    }

    public function getPath(array $path): array
    {
        $fileName = array_pop($path);
        return array_merge($path, [$this->config->getDescDir(), $fileName . $this->config->getDescExt()]);
    }
}
