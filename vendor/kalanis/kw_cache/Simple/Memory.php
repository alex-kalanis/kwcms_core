<?php

namespace kalanis\kw_cache\Simple;


use kalanis\kw_cache\Interfaces\ICache;


/**
 * Class Memory
 * @package kalanis\kw_cache\Simple
 * Caching content in memory
 */
class Memory implements ICache
{
    protected $resource = null;

    public function init(string $what): void
    {
    }

    public function exists(): bool
    {
        return !is_null($this->resource);
    }

    public function set(string $content): bool
    {
        $this->clear();
        $this->resource = fopen('php://temp', 'rb+');
        fwrite($this->resource, $content);
        return true;
    }

    public function get(): string
    {
        if ($this->exists()) {
            rewind($this->resource);
            return fgets($this->resource);
        } else {
            return '';
        }
    }

    public function clear(): void
    {
        if ($this->exists()) {
            fclose($this->resource);
        }
        $this->resource = null;
    }
}
