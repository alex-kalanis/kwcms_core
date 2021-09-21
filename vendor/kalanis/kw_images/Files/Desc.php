<?php

namespace kalanis\kw_images\Files;


use kalanis\kw_images\ImagesException;
use kalanis\kw_paths\Stuff;


/**
 * Class Desc
 * File description
 * @package kalanis\kw_images\Files
 */
class Desc extends AFiles
{
    /**
     * @param string $path
     * @return string
     * @throws ImagesException
     */
    public function get(string $path): string
    {
        $whatPath = $this->libExtendDir->getWebRootDir() . $this->getPath($path);
        if (!is_file($whatPath) || !is_readable($whatPath)) {
            return '';
        }
        $content = file_get_contents($whatPath);
        if (false === $content) {
            throw new ImagesException('Cannot read description');
        }
        return $content;
    }

    /**
     * @param string $path
     * @param string $content
     * @throws ImagesException
     */
    public function set(string $path, string $content): void
    {
        $whatPath = $this->libExtendDir->getWebRootDir() . $this->getPath($path);

        if (false === file_put_contents( $whatPath, $content )) {
            throw new ImagesException('Cannot add description');
        }
    }

    /**
     * @param string $path
     * @throws ImagesException
     */
    public function remove(string $path): void
    {
        $this->deleteFile($this->getPath($path), 'Cannot remove description!');
    }

    public function getPath(string $path): string
    {
        $filePath = Stuff::removeEndingSlash(Stuff::directory($path));
        $fileName = Stuff::filename($path);
        return $filePath . DIRECTORY_SEPARATOR . $this->libExtendDir->getDescDir() . DIRECTORY_SEPARATOR . $fileName . $this->libExtendDir->getDescExt();
    }
}
