<?php

namespace kalanis\kw_storage\Interfaces;


use kalanis\kw_storage\StorageException;


/**
 * Interface IFormat
 * @package kalanis\kw_storage\Interfaces
 * Format content into and from storage
 */
interface IFormat
{
    /**
     * @param mixed $content
     * @return mixed usually primitives like string or int
     * @throws StorageException
     */
    public function decode($content);

    /**
     * @param mixed $data usually primitives like string or int
     * @return mixed restored content
     * @throws StorageException
     */
    public function encode($data);
}
