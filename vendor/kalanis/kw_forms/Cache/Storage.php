<?php

namespace kalanis\kw_forms\Cache;


use kalanis\kw_storage\Interfaces\ITarget;
use kalanis\kw_storage\StorageException;


class Storage
{
    /** @var ITarget|null */
    protected $target = null;
    /** @var Key */
    protected $key = null;

    public function __construct(?ITarget $target = null)
    {
        $this->target = $target;
        $this->key = new Key();
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
     */
    public function store(array $values, ?int $timeout = null): void
    {
        if (!$this->target) {
            return;
        }
        $this->target->save($this->key->fromSharedKey(''), serialize($values), $timeout);
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
        $values = $this->target->load($this->key->fromSharedKey(''));
        $data = @unserialize($values);
        if (false === $data) {
            return [];
        }
        return $data;
    }

    /**
     * Remove data from storage
     * @throws StorageException
     */
    public function delete(): void
    {
        if (!$this->target) {
            return;
        }
        $this->target->remove($this->key->fromSharedKey(''));
    }
}
