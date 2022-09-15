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
     * @param string[] $name
     * @param string $suffix
     * @throws FilesException
     * @return string
     */
    public function findFreeName(array $name, string $suffix): string;

    /**
     * @param string[] $entry
     * @param string|resource $content
     * @throws FilesException
     * @return bool
     */
    public function saveFile(array $entry, $content): bool;

    /**
     * @param string[] $entry
     * @param int|null $offset
     * @param int|null $length
     * @throws FilesException
     * @return string|resource
     */
    public function readFile(array $entry, ?int $offset = null, ?int $length = null);

    /**
     * @param string[] $source
     * @param string[] $dest
     * @throws FilesException
     * @return bool
     */
    public function copyFile(array $source, array $dest): bool;

    /**
     * @param string[] $source
     * @param string[] $dest
     * @throws FilesException
     * @return bool
     */
    public function moveFile(array $source, array $dest): bool;

    /**
     * @param string[] $entry
     * @throws FilesException
     * @return bool
     */
    public function deleteFile(array $entry): bool;
}
