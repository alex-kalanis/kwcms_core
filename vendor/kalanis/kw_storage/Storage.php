<?php

namespace kalanis\kw_storage;


use kalanis\kw_storage\Interfaces\IStorage;
use Traversable;


/**
 * Class Storage
 * @package kalanis\kw_storage
 * Main storage class
 */
class Storage
{
    /** @var IStorage|null */
    protected $storage = null;
    /** @var Storage\Factory */
    protected $storageFactory = null;

    public function __construct(Storage\Factory $storageFactory)
    {
        $this->storageFactory = $storageFactory;
    }

    /**
     * @param mixed|Interfaces\ITarget|array|string|null $storageParams
     */
    public function init($storageParams): void
    {
        $this->storage = $this->storageFactory->getStorage($storageParams);
    }

    /**
     * If entry exists in storage
     * @param string $key
     * @throws StorageException
     * @return boolean
     */
    public function exists(string $key): bool
    {
        $this->checkStorage();
        return $this->storage->/** @scrutinizer ignore-call */exists($key);
    }

    /**
     * Get data from storage
     * @param string $key
     * @throws StorageException
     * @return mixed
     */
    public function get(string $key)
    {
        $this->checkStorage();
        $content = $this->storage->/** @scrutinizer ignore-call */read($key);
        return empty($content) ? null : $content ;
    }

    /**
     * Set data to storage
     * @param string $key
     * @param mixed $value
     * @param int $expire
     * @throws StorageException
     * @return boolean
     */
    public function set(string $key, $value, ?int $expire = 8600): bool
    {
        $this->checkStorage();
        return $this->storage->/** @scrutinizer ignore-call */write($key, $value, $expire);
    }

    /**
     * Add data to storage
     * @param string $key
     * @param mixed $value
     * @param int $expire
     * @throws StorageException
     * @return boolean
     */
    public function add(string $key, $value, ?int $expire = 8600): bool
    {
        $this->checkStorage();
        // safeadd for multithread at any system
        if ($this->storage->/** @scrutinizer ignore-call */write($key, $value, $expire)) {
            return ( $value == $this->get($key) );
        }
        return false;
    }

    /**
     * Increment value by key
     * @param string $key
     * @throws StorageException
     * @return boolean
     */
    public function increment(string $key): bool
    {
        $this->checkStorage();
        return $this->storage->/** @scrutinizer ignore-call */increment($key);
    }

    /**
     * Decrement value by key
     * @param string $key
     * @throws StorageException
     * @return boolean
     */
    public function decrement(string $key): bool
    {
        $this->checkStorage();
        return $this->storage->/** @scrutinizer ignore-call */decrement($key);
    }

    /**
     * Return all active storage keys
     * @throws StorageException
     * @return Traversable<string>
     */
    public function getAllKeys(): Traversable
    {
        return $this->getMaskedKeys('');
    }

    /**
     * Return storage keys with mask
     * @param string $mask
     * @throws StorageException
     * @return Traversable<string>
     */
    public function getMaskedKeys(string $mask): Traversable
    {
        $this->checkStorage();
        return $this->storage->/** @scrutinizer ignore-call */lookup($mask);
    }

    /**
     * Delete data by key from storage
     * @param string $key
     * @throws StorageException
     * @return boolean
     */
    public function delete(string $key): bool
    {
        $this->checkStorage();
        return $this->storage->/** @scrutinizer ignore-call */remove($key);
    }

    /**
     * Delete multiple keys from storage
     * @param string[] $keys
     * @throws StorageException
     * @return array<int|string, bool>
     */
    public function deleteMulti(array $keys)
    {
        $this->checkStorage();
        return $this->storage->/** @scrutinizer ignore-call */removeMulti($keys);
    }

    /**
     * Delete all data from storage where key starts with prefix
     * @param string $prefix
     * @param boolean $inverse - if true remove all data where keys doesn't starts with prefix
     * @throws StorageException
     * @codeCoverageIgnore mock has no keys for now
     */
    public function deleteByPrefix(string $prefix, $inverse = false): void
    {
        $keysToDelete = [];
        foreach ($this->getAllKeys() as $memKey) {
            $find = strpos($memKey, $prefix);
            if ((! $inverse && 0 === $find) || ($inverse && (false === $find || 0 !== $find))) {
                $keysToDelete[] = $memKey;
            }
        }
        $this->deleteMulti($keysToDelete);
    }

    /**
     * Check connection status to storage
     * @throws StorageException
     * @return boolean
     */
    public function isConnected(): bool
    {
        $this->checkStorage();
        return $this->storage->/** @scrutinizer ignore-call */canUse();
    }

    /**
     * @throws StorageException
     */
    protected function checkStorage(): void
    {
        if (empty($this->storage)) {
            throw new StorageException('Storage not initialized');
        }
    }
}
