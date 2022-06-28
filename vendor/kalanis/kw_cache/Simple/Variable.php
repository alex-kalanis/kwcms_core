<?php

namespace kalanis\kw_cache\Simple;


use kalanis\kw_cache\Interfaces\ICache;


/**
 * Class Variable
 * @package kalanis\kw_cache\Simple
 * Caching content in variable
 */
class Variable implements ICache
{
    /** @var string|null */
    protected $content = null;

    public function init(string $what): void
    {
    }

    public function exists(): bool
    {
        return !is_null($this->content);
    }

    public function set(string $content): bool
    {
        $this->content = $content;
        return true;
    }

    public function get(): string
    {
        return strval($this->content);
    }

    public function clear(): void
    {
        $this->content = null;
    }
}
