<?php

namespace kalanis\kw_images\Sources;


use kalanis\kw_files\FilesException;
use kalanis\kw_paths\Stuff;


/**
 * Class DirThumb
 * Directory thumbnail
 * @package kalanis\kw_images\Sources
 */
class DirThumb extends AFiles
{
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
        return $this->libProcessor->getFileProcessor()->saveFile($this->getPath($path), $content);
    }

    /**
     * @param string $whichDir
     * @throws FilesException
     */
    public function delete(string $whichDir): void
    {
        $whatPath = $this->getPath($whichDir);
        $this->dataRemove($whatPath, $this->getLang()->imDirThumbCannotRemove());
    }

    public function getPath(string $path): array
    {
        return Stuff::pathToArray(Stuff::removeEndingSlash($path)) + [
            $this->config->getThumbDir(),
            $this->config->getDescFile() . $this->config->getThumbExt()
        ];
    }
}
