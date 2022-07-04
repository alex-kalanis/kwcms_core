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
     * @param mixed|Interfaces\IStorage|array|string|null $storageParams
     * @return Storage|null
     */
    public function getStorage($storageParams): ?Storage
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
