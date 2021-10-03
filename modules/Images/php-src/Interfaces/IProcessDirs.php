<?php

namespace KWCMS\modules\Images\Interfaces;


use kalanis\kw_images\ImagesException;


/**
 * Interface IProcessDirs
 * @package KWCMS\modules\Images\Interfaces
 * Process dirs which represent galleries
 */
interface IProcessDirs
{
    /**
     * @param string $dirPath
     * @return string
     * @throws ImagesException
     */
    public function getDesc(string $dirPath): string;

    /**
     * @param string $dirPath
     * @param string $content
     * @throws ImagesException
     */
    public function updateDesc(string $dirPath, string $content): void;

    /**
     * @param string $dirPath
     * @return string
     * @throws ImagesException
     */
    public function getThumb(string $dirPath): string;

    /**
     * @param string $filePath
     * @return bool
     * @throws ImagesException
     */
    public function updateThumb(string $filePath): bool;
}
