<?php

namespace kalanis\kw_files\Interfaces;


use kalanis\kw_files\FilesException;


/**
 * Interface IProcessDirs
 * @package kalanis\kw_files\Interfaces
 * Process dirs in basic ways
 */
interface IProcessDirs
{
    /**
     * @param string $entry
     * @param bool $deep
     * @throws FilesException
     * @return bool
     */
    public function createDir(string $entry, bool $deep = false): bool;

    /**
     * @param string $entry
     * @param bool $loadRecursive
     * @throws FilesException
     * @return array<string>
     */
    public function readDir(string $entry, bool $loadRecursive = false): array;

    /**
     * @param string $source
     * @param string $dest
     * @throws FilesException
     * @return bool
     */
    public function copyDir(string $source, string $dest): bool;

    /**
     * @param string $source
     * @param string $dest
     * @throws FilesException
     * @return bool
     */
    public function moveDir(string $source, string $dest): bool;

    /**
     * @param string $entry
     * @param bool $deep
     * @throws FilesException
     * @return bool
     */
    public function deleteDir(string $entry, bool $deep = false): bool;
}
