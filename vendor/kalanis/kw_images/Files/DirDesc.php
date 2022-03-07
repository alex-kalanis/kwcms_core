<?php

namespace kalanis\kw_images\Files;


use kalanis\kw_images\ImagesException;
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
     * @return string
     * @throws ImagesException
     */
    public function get(string $path): string
    {
        $descPath = $this->libExtendDir->getWebRootDir() . $this->getPath($path);
        if (is_null($this->isUsable($descPath))) {
            return '';
        }
        $content = file_get_contents($descPath);
        if (false === $content) {
            // @codeCoverageIgnoreStart
            throw new ImagesException($this->getLang()->imDirDescCannotRead());
        }
        // @codeCoverageIgnoreEnd
        return $content;
    }

    /**
     * @param string $path
     * @param string $content
     * @return bool
     * @throws ImagesException
     */
    public function set(string $path, string $content): bool
    {
        $descPath = $this->libExtendDir->getWebRootDir() . $this->getPath($path);
        $this->isUsable($descPath);

        if (false === @file_put_contents($descPath, $content)) {
            // @codeCoverageIgnoreStart
            throw new ImagesException($this->getLang()->imDirDescCannotAdd());
        }
        // @codeCoverageIgnoreEnd
        return true;
    }

    /**
     * @param string $path
     * @return bool
     * @throws ImagesException
     */
    public function remove(string $path): bool
    {
        $descPath = $this->libExtendDir->getWebRootDir() . $this->getPath($path);
        if ($this->libExtendDir->isFile($descPath) && !unlink($descPath)) {
            // @codeCoverageIgnoreStart
            throw new ImagesException($this->getLang()->imDirDescCannotRemove());
        }
        // @codeCoverageIgnoreEnd
        return true;
    }

    public function canUse(string $path): bool
    {
        $descPath = $this->libExtendDir->getWebRootDir() . Stuff::removeEndingSlash($path) . DIRECTORY_SEPARATOR . $this->libExtendDir->getDescDir();
        return $this->libExtendDir->isDir($descPath) && is_readable($descPath) && is_writable($descPath);
    }

    /**
     * @param string $path
     * @return bool|null
     * @throws ImagesException
     */
    protected function isUsable(string $path): ?bool
    {
        if (is_readable($path) && is_writable($path)) {
            return true;
        }
        $dir = Stuff::removeEndingSlash(Stuff::directory($path));
        if (!file_exists($path) && is_readable($dir) && is_writable($dir)) {
            return null;
        }
        throw new ImagesException($this->getLang()->imDirDescCannotAccess());
    }

    public function getPath(string $path): string
    {
        return Stuff::removeEndingSlash($path) . DIRECTORY_SEPARATOR . $this->libExtendDir->getDescDir()
            . DIRECTORY_SEPARATOR . $this->libExtendDir->getDescFile() . $this->libExtendDir->getDescExt();
    }
}
