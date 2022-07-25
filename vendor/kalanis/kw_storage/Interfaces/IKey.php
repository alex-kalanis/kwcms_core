<?php

namespace kalanis\kw_storage\Interfaces;


/**
 * Interface IKey
 * @package kalanis\kw_storage\Interfaces
 * Set rules for key change and apply them
 * It's something like prefix or root dir for accessing everything
 */
interface IKey
{
    /**
     * @param string $key what app think
     * @return string real key for storage
     */
    public function fromSharedKey(string $key): string;
}
