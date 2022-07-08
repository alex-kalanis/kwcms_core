<?php

namespace kalanis\kw_storage\Interfaces;


use kalanis\kw_storage\StorageException;


/**
 * Interface IPassDirs
 * @package kalanis\kw_storage\Interfaces
 * When storage differs dirs and files (like normal volume)
 */
interface IPassDirs
{
    /**
     * @param string $key
     * @return bool
     */
    public function isDir(string $key): bool;

    /**
     * Create subdir
     * @param string $key
     * @param bool $recursive
     * @throws StorageException
     * @return bool
     */
    public function mkDir(string $key, bool $recursive = false): bool;

    /**
     * Remove subdir
     * @param string $key
     * @param bool $recursive
     * @throws StorageException
     * @return bool
     */
    public function rmDir(string $key, bool $recursive = false): bool;

    /**
     * Copy dirs and files
     * @param string $source
     * @param string $dest
     * @throws StorageException
     * @return bool
     */
    public function copy(string $source, string $dest): bool;

    /**
     * Move dirs and files
     * @param string $source
     * @param string $dest
     * @throws StorageException
     * @return bool
     */
    public function move(string $source, string $dest): bool;

    /**
     * Get node size
     * @param string $key
     * @throws StorageException
     * @return int
     */
    public function size(string $key): int;
}
