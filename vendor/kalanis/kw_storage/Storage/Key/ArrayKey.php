<?php

namespace kalanis\kw_storage\Storage\Key;


use kalanis\kw_storage\Interfaces\IKey;


/**
 * Class ArrayKey
 * @package kalanis\kw_storage\Key
 * Added prefix path - in form of array
 */
class ArrayKey implements IKey
{
    protected string $prefix = '';

    /**
     * @param string[] $prefix
     * @param string $separator
     */
    public function __construct(array $prefix, string $separator = DIRECTORY_SEPARATOR)
    {
        $this->prefix = implode($separator, $prefix);
    }

    public function fromSharedKey(string $key): string
    {
        return $this->prefix . $key;
    }
}
