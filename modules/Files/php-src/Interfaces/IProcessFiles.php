<?php

namespace KWCMS\modules\Files\Interfaces;


use kalanis\kw_files\FilesException;
use kalanis\kw_input\Interfaces\IFileEntry;


/**
 * Interface IProcessFiles
 * @package KWCMS\modules\Files\Lib
 * Process files in basic ways
 */
interface IProcessFiles
{
    const FREE_NAME_SEPARATOR = '_';

    /**
     * @param string $name
     * @return string
     * @throws FilesException
     */
    public function findFreeName(string $name): string;

    /**
     * @param IFileEntry $file
     * @param string $targetName
     * @return bool
     * @throws FilesException
     */
    public function uploadFile(IFileEntry $file, string $targetName): bool;

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
