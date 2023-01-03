<?php

namespace kalanis\kw_images\Sources;


use kalanis\kw_files\FilesException;


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
     * @param string[] $path
     * @throws FilesException
     * @return bool
     */
    public function remove(array $path): bool
    {
        return $this->libFile->deleteFile($this->getPath($path));
    }

    /**
     * @param string[] $path
     * @throws FilesException
     * @return bool
     */
    public function canUse(array $path): bool
    {
        return $this->libNode->isDir(array_merge($path, [$this->config->getDescDir()]));
    }

    public function getPath(array $path): array
    {
        return array_merge($path, [$this->config->getDescDir(), $this->config->getDescFile() . $this->config->getDescExt()]);
    }
}
