<?php

namespace kalanis\kw_cache\Interfaces;


use kalanis\kw_storage\StorageException;


/**
 * Interface ICache
 * @package kalanis\kw_cache\Interfaces
 * Interface for caching classes
 */
interface ICache
{
    const EXT_CACHE = '.cache'; # cache itself file ext
    const EXT_RELOAD = '.reload'; # reload file ext

    /**
     * @param string $what
     * @throws StorageException
     */
    public function init(string $what): void;

    /**
     * @return bool
     * @throws StorageException
     */
    public function exists(): bool;

    /**
     * @param string $content
     * @return bool
     * @throws StorageException
     */
    public function set(string $content): bool;

    /**
     * @return string
     * @throws StorageException
     */
    public function get(): string;

    /**
     * @throws StorageException
     */
    public function clear(): void;
}
