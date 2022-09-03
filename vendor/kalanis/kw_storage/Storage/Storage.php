<?php

namespace kalanis\kw_storage\Storage;


use kalanis\kw_storage\Interfaces;
use Traversable;


/**
 * Class Storage
 * @package kalanis\kw_storage\Storage
 * Main connection through storage structure
 */
class Storage implements Interfaces\IStorage
{
    /** @var Interfaces\ITarget */
    protected $target = null;
    /** @var Interfaces\IKey */
    protected $key = null;

    public function __construct(Interfaces\IKey $key, Interfaces\ITarget $target)
    {
        $this->target = $target;
        $this->key = $key;
    }

    public function canUse(): bool
    {
        return $this->target->check($this->key->fromSharedKey(''));
    }

    public function write(string $sharedKey, $data, ?int $timeout = null): bool
    {
        return $this->target->save($this->key->fromSharedKey($sharedKey), $data, $timeout);
    }

    public function read(string $sharedKey)
    {
        return $this->target->load($this->key->fromSharedKey($sharedKey));
    }

    public function remove(string $sharedKey): bool
    {
        return $this->target->remove($this->key->fromSharedKey($sharedKey));
    }

    public function exists(string $sharedKey): bool
    {
        return $this->target->exists($this->key->fromSharedKey($sharedKey));
    }

    public function lookup(string $mask): Traversable
    {
        return $this->target->lookup($this->key->fromSharedKey($mask));
    }

    public function increment(string $key): bool
    {
        return $this->target->increment($this->key->fromSharedKey($key));
    }

    public function decrement(string $key): bool
    {
        return $this->target->decrement($this->key->fromSharedKey($key));
    }

    public function removeMulti(array $keys): array
    {
        return $this->target->removeMulti(array_map([$this, 'multiKey'], $keys));
    }

    public function multiKey(string $key): string
    {
        return $this->key->fromSharedKey($key);
    }
}
