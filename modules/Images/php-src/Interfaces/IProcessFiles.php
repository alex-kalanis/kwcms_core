<?php

namespace KWCMS\modules\Images\Interfaces;


use kalanis\kw_images\ImagesException;
use kalanis\kw_input\Interfaces\IFileEntry;


/**
 * Interface IProcessFiles
 * @package KWCMS\modules\Images\Interfaces
 * Process files in basic ways
 */
interface IProcessFiles
{
    const FREE_NAME_SEPARATOR = '_';

    /**
     * @param IFileEntry $file
     * @param string $desc
     * @return bool
     * @throws ImagesException
     */
    public function uploadFile(IFileEntry $file, string $desc): bool;

    /**
     * @param string $entry
     * @return string
     * @throws ImagesException
     */
    public function readDesc(string $entry): string;

    /**
     * @param string $entry
     * @param string $content
     * @throws ImagesException
     */
    public function updateDesc(string $entry, string $content): void;

    /**
     * @param string $entry
     * @param string $to
     * @return bool
     * @throws ImagesException
     */
    public function copyFile(string $entry, string $to): bool;

    /**
     * @param string $entry
     * @param string $to
     * @return bool
     * @throws ImagesException
     */
    public function moveFile(string $entry, string $to): bool;

    /**
     * @param string $entry
     * @param string $to
     * @return bool
     * @throws ImagesException
     */
    public function renameFile(string $entry, string $to): bool;

    /**
     * @param string $entry
     * @return bool
     * @throws ImagesException
     */
    public function deleteFile(string $entry): bool;
}
