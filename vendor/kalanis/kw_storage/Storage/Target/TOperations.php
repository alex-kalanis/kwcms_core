<?php

namespace kalanis\kw_storage\Storage\Target;


/**
 * Trait TOperations
 * @package kalanis\kw_storage\Storage\Target
 * Operations over storages - what is similar
 */
trait TOperations
{
    abstract public function exists(string $key): bool;

    abstract public function remove(string $key): bool;

    public function removeMulti(array $keys): array
    {
        $result = [];
        foreach ($keys as $index => $key) {
            $result[$index] = $this->exists($key) ? $this->remove($key) : false;
        }
        return $result;
    }
}
