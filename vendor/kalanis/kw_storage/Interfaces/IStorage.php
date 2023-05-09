<?php

namespace kalanis\kw_storage\Interfaces;


use kalanis\kw_storage\StorageException;
use Traversable;


/**
 * Interface IStorage
 * @package kalanis\kw_storage\Interfaces
 * Basic operations over every storage
 */
interface IStorage
{
    /**
     * Check if target storage is usable
     * @return bool
     */
    public function canUse(): bool;

    /**
     * Create new record in storage
     * @param string $sharedKey
     * @param mixed $data
     * @param int|null $timeout
     * @throws StorageException
     * @return bool
     */
    public function write(string $sharedKey, $data, ?int $timeout = null): bool;

    /**
     * Read storage record
     * @param string $sharedKey
     * @throws StorageException
     * @return mixed
     */
    public function read(string $sharedKey);

    /**
     * Delete storage record - usually on finish or discard
     * @param string $sharedKey
     * @throws StorageException
     * @return bool
     */
    public function remove(string $sharedKey): bool;

    /**
     * Has data in storage? Mainly for testing
     * @param string $sharedKey
     * @throws StorageException
     * @return bool
     */
    public function exists(string $sharedKey): bool;

    /**
     * What data is in storage?
     * @param string $mask
     * @throws StorageException
     * @return Traversable<string>
     */
    public function lookup(string $mask): Traversable;

    /**
     * Increment index in key
     * @param string $key
     * @throws StorageException
     * @return bool
     */
    public function increment(string $key): bool;

    /**
     * Decrement index in key
     * @param string $key
     * @throws StorageException
     * @return bool
     */
    public function decrement(string $key): bool;

    /**
     * Remove multiple keys
     * @param string[] $keys
     * @throws StorageException
     * @return array<int|string, bool>
     */
    public function removeMulti(array $keys): array;

    /**
     * Is storage flat table or deep tree structure
     * (is necessary to extra ask for get deeper levels in lookup or it already returns that nodes)
     * @return bool
     */
    public function isFlat(): bool;
}
