<?php

namespace kalanis\kw_images\Files;


use kalanis\kw_extras\ExtendDir;
use kalanis\kw_extras\ExtrasException;
use kalanis\kw_paths\Stuff;


/**
 * Class DirDesc
 * Directory description
 * @package kalanis\kw_images\Files
 */
class DirDesc extends AFiles
{
    /**
     * @param string $path
     * @param string $content
     * @return bool
     * @throws ExtrasException
     */
    public function set(string $path, string $content): bool
    {
        return $this->libExtendDir->setDirDescription($path, $content);
    }

    /**
     * @param string $path
     * @return string
     * @throws ExtrasException
     */
    public function get(string $path): string
    {
        return $this->libExtendDir->getDirDescription($path);
    }

    /**
     * @param string $path
     * @return string
     * @throws ExtrasException
     */
    public function remove(string $path): string
    {
        return $this->libExtendDir->removeDirDescription($path);
    }

    public function getPath(string $path): string
    {
        return Stuff::removeEndingSlash($path) . DIRECTORY_SEPARATOR . $this->libExtendDir->getDescDir()
            . DIRECTORY_SEPARATOR . $this->libExtendDir->getDescFile() . $this->libExtendDir->getDescExt();
    }

    public function getExtendDir(): ExtendDir
    {
        return $this->libExtendDir;
    }
}
