<?php

namespace kalanis\kw_images\Sources;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Traits\TToString;
use kalanis\kw_paths\PathsException;


/**
 * Class DirThumb
 * Directory thumbnail
 * @package kalanis\kw_images\Sources
 */
class DirThumb extends AFiles
{
    use TToString;

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
     * @param string[] $whichDir
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function delete(array $whichDir): bool
    {
        $whatPath = $this->getPath($whichDir);
        return $this->dataRemove($whatPath, $this->getImLang()->imDirThumbCannotRemove());
    }

    public function getPath(array $path): array
    {
        return array_merge($path, [$this->config->getThumbDir(), $this->config->getDescFile() . $this->config->getThumbExt()]);
    }
}
