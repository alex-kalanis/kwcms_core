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
            throw new ImagesException('Cannot read dir desc!');
        }
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
        if (is_null($this->isUsable($descPath))) {
            return false;
        }

        if (false === file_put_contents($descPath, $content)) {
            throw new ImagesException('Cannot write dir desc!');
        }
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
        if (is_file($descPath) && !unlink($descPath)) {
            throw new ImagesException('Cannot remove dir desc!');
        }
        return true;
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
        throw new ImagesException('Cannot access that file!');
    }

    public function getPath(string $path): string
    {
        return Stuff::removeEndingSlash($path) . DIRECTORY_SEPARATOR . $this->libExtendDir->getDescDir()
            . DIRECTORY_SEPARATOR . $this->libExtendDir->getDescFile() . $this->libExtendDir->getDescExt();
    }
}
