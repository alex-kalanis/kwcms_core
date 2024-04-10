<?php

namespace kalanis\kw_storage\Storage\Key;


use kalanis\kw_storage\Interfaces\IKey;


/**
 * Class DirKey
 * @package kalanis\kw_storage\Storage\Key
 * The key is part of a directory path - fill it
 */
class DirKey implements IKey
{
    protected string $path = '';

    public function __construct(string $dir)
    {
        $this->path = $dir;
    }

    public function fromSharedKey(string $key): string
    {
        return $this->path . $key;
    }
}
