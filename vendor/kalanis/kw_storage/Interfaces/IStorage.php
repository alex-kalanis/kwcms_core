<?php

namespace kalanis\kw_storage\Interfaces;


use kalanis\kw_storage\StorageException;
use Traversable;


interface IStorage
{
    /**
     * @param string $key
     * @return bool
     */
    public function check(string $key): bool;

    /**
     * @param string $key
     * @return bool
     */
    public function exists(string $key): bool;

    /**
     * @param string $key
     * @return string
     * @throws StorageException
     */
    public function load(string $key);

    /**
     * @param string $key
     * @param string $data
     * @param int|null $timeout
     * @return bool
     * @throws StorageException
     */
    public function save(string $key, $data, ?int $timeout = null): bool;

    /**
     * @param string $key
     * @return bool
     * @throws StorageException
     */
    public function remove(string $key): bool;

    /**
     * Lookup through keys in storage
     * @param string $key
     * @return Traversable
     * @throws StorageException
     */
    public function lookup(string $key): Traversable;

    /**
     * Increment index in key
     * @param string $key
     * @return bool
     * @throws StorageException
     */
    public function increment(string $key): bool;

    /**
     * Decrement index in key
     * @param string $key
     * @return bool
     * @throws StorageException
     */
    public function decrement(string $key): bool;

    /**
     * Remove multiple keys
     * @param string[] $keys
     * @return string[]
     * @throws StorageException
     */
    public function removeMulti(array $keys): array;
}
