<?php

namespace kalanis\kw_images\Sources;


use kalanis\kw_files\FilesException;


/**
 * Class DirThumb
 * Directory thumbnail
 * @package kalanis\kw_images\Sources
 */
class DirThumb extends AFiles
{
    /**
     * @param string[] $path
     * @throws FilesException
     * @return string|resource
     */
    public function get(array $path)
    {
        return $this->libFile->readFile($this->getPath($path));
    }

    /**
     * @param string[] $path
     * @param string|resource $content
     * @throws FilesException
     * @return bool
     */
    public function set(array $path, $content): bool
    {
        return $this->libFile->saveFile($this->getPath($path), $content);
    }

    /**
     * @param string[] $whichDir
     * @throws FilesException
     * @return bool
     */
    public function delete(array $whichDir): bool
    {
        $whatPath = $this->getPath($whichDir);
        return $this->dataRemove($whatPath, $this->getLang()->imDirThumbCannotRemove());
    }

    public function getPath(array $path): array
    {
        return array_merge($path, [$this->config->getThumbDir(), $this->config->getDescFile() . $this->config->getThumbExt()]);
    }
}
