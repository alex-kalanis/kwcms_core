<?php

namespace kalanis\kw_images\Sources;


use kalanis\kw_files\FilesException;
use kalanis\kw_paths\PathsException;


/**
 * Class DirDesc
 * Directory description
 * @package kalanis\kw_images\Sources
 */
class DirDesc extends AFiles
{
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
            $content = $this->lib->readFile($this->getPath($path));
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
     * @throws PathsException
     * @return bool
     */
    public function set(array $path, string $content): bool
    {
        return $this->lib->saveFile($this->getPath($path), $content);
    }

    /**
     * @param string[] $path
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function remove(array $path): bool
    {
        return $this->lib->deleteFile($this->getPath($path));
    }

    /**
     * @param string[] $path
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function canUse(array $path): bool
    {
        return $this->lib->isDir(array_merge($path, [$this->config->getDescDir()]));
    }

    public function getPath(array $path): array
    {
        return array_merge($path, [$this->config->getDescDir(), $this->config->getDescFile() . $this->config->getDescExt()]);
    }
}
