<?php

namespace KWCMS\modules\Images\Interfaces;


use kalanis\kw_files\FilesException;
use kalanis\kw_images\ImagesException;
use kalanis\kw_mime\MimeException;
use kalanis\kw_paths\PathsException;


/**
 * Interface IProcessDirs
 * @package KWCMS\modules\Images\Interfaces
 * Process dirs which represent galleries
 */
interface IProcessDirs
{
    /**
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function canUse(): bool;

    /**
     * @param string $target
     * @param string $name
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function createDir(string $target, string $name): bool;

    /**
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function createExtra(): bool;

    /**
     * @throws FilesException
     * @throws PathsException
     * @return string
     */
    public function getDesc(): string;

    /**
     * @param string $content
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function updateDesc(string $content): bool;

    /**
     * @throws FilesException
     * @throws PathsException
     * @return string|resource
     */
    public function getThumb();

    /**
     * @param string $filePath
     * @throws FilesException
     * @throws ImagesException
     * @throws MimeException
     * @throws PathsException
     * @return bool
     */
    public function updateThumb(string $filePath): bool;

    /**
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function removeThumb(): bool;
}
