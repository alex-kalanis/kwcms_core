<?php

namespace kalanis\kw_cache\Interfaces;


use kalanis\kw_cache\CacheException;


/**
 * Interface IFormat
 * @package kalanis\kw_cache\Interfaces
 * Format content into and from storage
 */
interface IFormat
{
    /**
     * @param mixed $content
     * @throws CacheException
     * @return mixed usually primitives like string or int
     */
    public function unpack($content);

    /**
     * @param mixed $data usually primitives like string or int
     * @throws CacheException
     * @return mixed stored content
     */
    public function pack($data);
}
