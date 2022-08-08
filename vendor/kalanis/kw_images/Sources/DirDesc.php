<?php

namespace kalanis\kw_images\Sources;


use kalanis\kw_files\FilesException;
use kalanis\kw_paths\Stuff;


/**
 * Class DirDesc
 * Directory description
 * @package kalanis\kw_images\Sources
 */
class DirDesc extends AFiles
{
    /**
     * @param string $path
     * @param bool $errorOnFail
     * @throws FilesException
     * @return string|resource
     */
    public function get(string $path, bool $errorOnFail = false)
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
     * @param string $path
     * @throws FilesException
     * @return bool
     */
    public function remove(string $path): bool
    {
        return $this->libProcessor->getFileProcessor()->deleteFile($this->getPath($path));
    }

    /**
     * @param string $path
     * @throws FilesException
     * @return bool
     */
    public function canUse(string $path): bool
    {
        $descPath = Stuff::removeEndingSlash($path) . DIRECTORY_SEPARATOR . $this->config->getDescDir();
        return $this->libProcessor->getNodeProcessor()->isDir(Stuff::pathToArray($descPath));
    }

    public function getPath(string $path): array
    {
        return Stuff::pathToArray(Stuff::removeEndingSlash($path)) + [
            $this->config->getDescDir(),
            $this->config->getDescFile() . $this->config->getDescExt()
        ];
    }
}
