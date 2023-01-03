<?php

namespace KWCMS\modules\Images\Interfaces;


use kalanis\kw_files\FilesException;
use kalanis\kw_images\ImagesException;


/**
 * Interface IProcessDirs
 * @package KWCMS\modules\Images\Interfaces
 * Process dirs which represent galleries
 */
interface IProcessDirs
{
    /**
     * @throws FilesException
     * @return bool
     */
    public function canUse(): bool;

    /**
     * @param string $target
     * @param string $name
     * @throws FilesException
     * @return bool
     */
    public function createDir(string $target, string $name): bool;

    /**
     * @throws FilesException
     * @return bool
     */
    public function createExtra(): bool;

    /**
     * @throws FilesException
     * @return string
     */
    public function getDesc(): string;

    /**
     * @param string $content
     * @throws FilesException
     * @return bool
     */
    public function updateDesc(string $content): bool;

    /**
     * @throws FilesException
     * @return string|resource
     */
    public function getThumb();

    /**
     * @param string $filePath
     * @throws FilesException
     * @throws ImagesException
     * @return bool
     */
    public function updateThumb(string $filePath): bool;

    /**
     * @throws FilesException
     * @return bool
     */
    public function removeThumb(): bool;
}
