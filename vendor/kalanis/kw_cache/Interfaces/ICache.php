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
     * @throws StorageException
     * @return bool
     */
    public function exists(): bool;

    /**
     * @param string $content
     * @throws StorageException
     * @return bool
     */
    public function set(string $content): bool;

    /**
     * @throws StorageException
     * @return string
     */
    public function get(): string;

    /**
     * @throws StorageException
     */
    public function clear(): void;
}
