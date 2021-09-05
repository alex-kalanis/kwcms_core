<?php

namespace KWCMS\modules\Files\Interfaces;


use kalanis\kw_input\Interfaces\IFileEntry;
use KWCMS\modules\Files\FilesException;


/**
 * Interface IProcessFiles
 * @package KWCMS\modules\Files\Lib
 * Process files in basic ways
 */
interface IProcessFiles
{
    const FREE_NAME_SEPARATOR = '_';

    /**
     * @param IFileEntry $file
     * @return bool
     * @throws FilesException
     */
    public function uploadFile(IFileEntry $file): bool;

    /**
     * @param string $entry
     * @param int|null $offset
     * @param int|null $length
     * @return string
     * @throws FilesException
     */
    public function readFile(string $entry, ?int $offset = null, ?int $length = null): string;

    /**
     * @param string $entry
     * @param string $to
     * @return bool
     * @throws FilesException
     */
    public function copyFile(string $entry, string $to): bool;

    /**
     * @param string $entry
     * @param string $to
     * @return bool
     * @throws FilesException
     */
    public function moveFile(string $entry, string $to): bool;

    /**
     * @param string $entry
     * @param string $to
     * @return bool
     * @throws FilesException
     */
    public function renameFile(string $entry, string $to): bool;

    /**
     * @param string $entry
     * @return bool
     * @throws FilesException
     */
    public function deleteFile(string $entry): bool;
}
