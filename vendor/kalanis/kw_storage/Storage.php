<?php

namespace kalanis\kw_storage;


use kalanis\kw_storage\Interfaces\IStorage;
use kalanis\kw_storage\Interfaces\IStTranslations;
use kalanis\kw_storage\Traits\TLang;
use Traversable;


/**
 * Class Storage
 * @package kalanis\kw_storage
 * Main storage class
 */
class Storage
{
    use TLang;

    protected ?IStorage $storage = null;
    protected Storage\Factory $storageFactory;

    public function __construct(Storage\Factory $storageFactory, ?IStTranslations $lang = null)
    {
        $this->storageFactory = $storageFactory;
        $this->setStLang($lang);
    }

    /**
     * @param object|array<string, string|object>|string|null $storageParams
     * @throws StorageException
     */
    public function init($storageParams): void
    {
        if (is_array($storageParams) && empty($storageParams['lang']) && $this->stLang) {
            $storageParams['lang'] = $this->stLang;
        }
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
        return $this->getStorage()->exists($key);
    }

    /**
     * Get data from storage
     * @param string $key
     * @throws StorageException
     * @return string|null
     */
    public function get(string $key): ?string
    {
        $content = $this->getStorage()->read($key);
        return empty($content) ? null : $content ;
    }

    /**
     * Set data to storage
     * @param string $key
     * @param string $value
     * @param int $expire
     * @throws StorageException
     * @return boolean
     */
    public function set(string $key, string $value, ?int $expire = 8600): bool
    {
        return $this->getStorage()->write($key, $value, $expire);
    }

    /**
     * Add data to storage
     * @param string $key
     * @param string $value
     * @param int $expire
     * @throws StorageException
     * @return boolean
     */
    public function add(string $key, string $value, ?int $expire = 8600): bool
    {
        // safeadd for multithread at any system
        if ($this->getStorage()->write($key, $value, $expire)) {
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
        return $this->getStorage()->increment($key);
    }

    /**
     * Decrement value by key
     * @param string $key
     * @throws StorageException
     * @return boolean
     */
    public function decrement(string $key): bool
    {
        return $this->getStorage()->decrement($key);
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
        return $this->getStorage()->lookup($mask);
    }

    /**
     * Delete data by key from storage
     * @param string $key
     * @throws StorageException
     * @return boolean
     */
    public function delete(string $key): bool
    {
        return $this->getStorage()->remove($key);
    }

    /**
     * Delete multiple keys from storage
     * @param string[] $keys
     * @throws StorageException
     * @return array<int|string, bool>
     */
    public function deleteMulti(array $keys)
    {
        return $this->getStorage()->removeMulti($keys);
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
        return $this->getStorage()->canUse();
    }

    /**
     * @throws StorageException
     */
    protected function getStorage(): IStorage
    {
        if (empty($this->storage)) {
            throw new StorageException($this->getStLang()->stStorageNotInitialized());
        }
        return $this->storage;
    }
}
