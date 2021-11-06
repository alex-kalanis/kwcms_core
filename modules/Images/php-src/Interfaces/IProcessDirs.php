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
     * @return bool
     * @throws ImagesException
     */
    public function canUse(): bool;

    /**
     * @param string $target
     * @param string $name
     * @return bool
     * @throws ImagesException
     */
    public function createDir(string $target, string $name): bool;

    /**
     * @return bool
     * @throws ImagesException
     */
    public function createExtra(): bool;

    /**
     * @return string
     * @throws ImagesException
     */
    public function getDesc(): string;

    /**
     * @param string $content
     * @return bool
     * @throws ImagesException
     */
    public function updateDesc(string $content): bool;

    /**
     * @return string
     * @throws ImagesException
     */
    public function getThumb(): string;

    /**
     * @param string $filePath
     * @return bool
     * @throws ImagesException
     */
    public function updateThumb(string $filePath): bool;
}
