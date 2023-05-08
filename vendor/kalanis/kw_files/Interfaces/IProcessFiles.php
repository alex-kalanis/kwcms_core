<?php

namespace kalanis\kw_files\Interfaces;


use kalanis\kw_files\FilesException;
use kalanis\kw_paths\PathsException;


/**
 * Interface IProcessFiles
 * @package kalanis\kw_files\Interfaces
 * Process files in basic ways
 */
interface IProcessFiles
{
    /**
     * @param string[] $entry
     * @param string|resource $content
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function saveFile(array $entry, $content): bool;

    /**
     * @param string[] $entry
     * @param int<0, max>|null $offset
     * @param int<0, max>|null $length
     * @throws FilesException
     * @throws PathsException
     * @return string|resource
     */
    public function readFile(array $entry, ?int $offset = null, ?int $length = null);

    /**
     * @param string[] $source
     * @param string[] $dest
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function copyFile(array $source, array $dest): bool;

    /**
     * @param string[] $source
     * @param string[] $dest
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function moveFile(array $source, array $dest): bool;

    /**
     * @param string[] $entry
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function deleteFile(array $entry): bool;
}
