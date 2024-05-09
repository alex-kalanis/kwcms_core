<?php

namespace kalanis\kw_storage\Storage;


use kalanis\kw_storage\Interfaces;
use kalanis\kw_storage\StorageException;


/**
 * Class Factory
 * @package kalanis\kw_storage\Storage
 * Storage config factory class
 */
class Factory
{
    protected Target\Factory $targetFactory;
    protected Key\Factory $keyFactory;

    public function __construct(Key\Factory $keyFactory, Target\Factory $targetFactory)
    {
        $this->targetFactory = $targetFactory;
        $this->keyFactory = $keyFactory;
    }

    /**
     * @param object|array<string, string|object>|string|null $storageParams
     * @throws StorageException
     * @return Interfaces\IStorage|null
     */
    public function getStorage($storageParams): ?Interfaces\IStorage
    {
        if (is_object($storageParams) && ($storageParams instanceof Interfaces\IStorage)) {
            return $storageParams;
        }

        $storage = $this->targetFactory->getStorage($storageParams);
        if (empty($storage)) {
            return null;
        }

        if ($storage instanceof Interfaces\ITargetVolume) {
            $publicStorage = new StorageDirs(
                $this->keyFactory->getKey($storage),
                $storage
            );
            $publicStorage->canUse();
            return $publicStorage;

        } else {
            $publicStorage = new Storage(
                $this->keyFactory->getKey($storage),
                $storage
            );
            $publicStorage->canUse();
            return $publicStorage;
        }
    }
}
