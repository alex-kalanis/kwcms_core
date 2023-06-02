<?php

namespace kalanis\kw_cache\Interfaces;


use kalanis\kw_cache\CacheException;


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
     * @param string[] $what
     * @throws CacheException
     */
    public function init(array $what): void;

    /**
     * @throws CacheException
     * @return bool
     */
    public function exists(): bool;

    /**
     * @param string $content
     * @throws CacheException
     * @return bool
     */
    public function set(string $content): bool;

    /**
     * @throws CacheException
     * @return string
     */
    public function get(): string;

    /**
     * @throws CacheException
     */
    public function clear(): void;
}
