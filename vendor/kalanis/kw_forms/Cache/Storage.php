<?php

namespace kalanis\kw_forms\Cache;


use kalanis\kw_forms\Cache\Formats\Serialize;
use kalanis\kw_forms\Interfaces\ICachedFormat;
use kalanis\kw_storage\Interfaces\ITarget;
use kalanis\kw_storage\StorageException;


class Storage
{
    /** @var ITarget|null */
    protected $target = null;
    /** @var Key */
    protected $key = null;
    /** @var ICachedFormat */
    protected $format = null;

    public function __construct(?ITarget $target = null, ?ICachedFormat $format = null)
    {
        $this->target = $target;
        $this->key = new Key();
        $this->format = $format ?: new Serialize();
    }

    public function setAlias(string $alias = ''): void
    {
        $this->key->setAlias($alias);
    }

    /**
     * Check if data are stored
     * @throws StorageException
     * @return bool
     */
    public function isStored(): bool
    {
        if (!$this->target) {
            return false;
        }
        return $this->target->exists($this->key->fromSharedKey(''));
    }

    /**
     * Save form data into storage
     * @param array<string, string|int|float|bool|null> $values
     * @param int|null $timeout
     * @throws StorageException
     * @return bool
     */
    public function store(array $values, ?int $timeout = null): bool
    {
        if (!$this->target) {
            return false;
        }
        return $this->target->save($this->key->fromSharedKey(''), $this->format->pack($values), $timeout);
    }

    /**
     * Read data from storage
     * @throws StorageException
     * @return array<string, string|int|float|bool|null>
     */
    public function load(): array
    {
        if (!$this->target) {
            return [];
        }
        return $this->format->unpack($this->target->load($this->key->fromSharedKey('')));
    }

    /**
     * Remove data from storage
     * @throws StorageException
     * @return bool
     */
    public function delete(): bool
    {
        if (!$this->target) {
            return false;
        }
        return $this->target->remove($this->key->fromSharedKey(''));
    }
}
