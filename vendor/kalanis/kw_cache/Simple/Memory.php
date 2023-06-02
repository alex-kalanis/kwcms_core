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
    /** @var resource|null */
    protected $resource = null;

    public function init(array $what): void
    {
    }

    public function exists(): bool
    {
        return !is_null($this->resource);
    }

    public function set(string $content): bool
    {
        $this->clear();
        $resource = fopen('php://temp', 'rb+');
        if (false === $resource) {
            // @codeCoverageIgnoreStart
            return false;
        }
        // @codeCoverageIgnoreEnd
        fwrite($resource, $content);
        $this->resource = $resource;
        return true;
    }

    public function get(): string
    {
        if ($this->exists()) {
            rewind($this->resource);
            return strval(stream_get_contents($this->resource, -1, 0));
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
