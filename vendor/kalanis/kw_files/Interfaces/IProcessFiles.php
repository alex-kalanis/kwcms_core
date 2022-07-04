<?php

namespace kalanis\kw_files\Interfaces;


use kalanis\kw_files\FilesException;


/**
 * Interface IProcessFiles
 * @package kalanis\kw_files\Interfaces
 * Process files in basic ways
 */
interface IProcessFiles
{
    const FREE_NAME_SEPARATOR = '_';

    /**
     * @param string $name
     * @param string $suffix
     * @throws FilesException
     * @return string
     */
    public function findFreeName(string $name, string $suffix): string;

    /**
     * @param string $entry
     * @param string|resource $content
     * @throws FilesException
     * @return bool
     */
    public function saveFile(string $entry, $content): bool;

    /**
     * @param string $entry
     * @param int|null $offset
     * @param int|null $length
     * @throws FilesException
     * @return string
     */
    public function readFile(string $entry, ?int $offset = null, ?int $length = null): string;

    /**
     * @param string $source
     * @param string $dest
     * @throws FilesException
     * @return bool
     */
    public function copyFile(string $source, string $dest): bool;

    /**
     * @param string $source
     * @param string $dest
     * @throws FilesException
     * @return bool
     */
    public function moveFile(string $source, string $dest): bool;

    /**
     * @param string $entry
     * @throws FilesException
     * @return bool
     */
    public function deleteFile(string $entry): bool;
}
