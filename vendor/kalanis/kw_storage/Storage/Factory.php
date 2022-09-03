<?php

namespace kalanis\kw_storage\Storage;


use kalanis\kw_storage\Interfaces;


/**
 * Class Factory
 * @package kalanis\kw_storage\Storage
 * Storage config factory class
 */
class Factory
{
    /** @var Target\Factory */
    protected $targetFactory = null;
    /** @var Key\Factory */
    protected $keyFactory = null;

    public function __construct(Key\Factory $keyFactory, Target\Factory $targetFactory)
    {
        $this->targetFactory = $targetFactory;
        $this->keyFactory = $keyFactory;
    }

    /**
     * @param mixed|Interfaces\ITarget|array|string|null $storageParams
     * @return Interfaces\IStorage|null
     */
    public function getStorage($storageParams): ?Interfaces\IStorage
    {
        $storage = $this->targetFactory->getStorage($storageParams);
        if (empty($storage)) {
            return null;
        }
        $publicStorage = new Storage(
            $this->keyFactory->getKey($storage),
            $storage
        );
        $publicStorage->canUse();
        return $publicStorage;
    }
}
