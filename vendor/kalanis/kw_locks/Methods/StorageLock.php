<?php

namespace kalanis\kw_locks\Methods;


use kalanis\kw_locks\Interfaces\IKLTranslations;
use kalanis\kw_locks\Interfaces\IPassedKey;
use kalanis\kw_locks\LockException;
use kalanis\kw_locks\Traits\TLang;
use kalanis\kw_storage\Interfaces\IStorage;
use kalanis\kw_storage\StorageException;


/**
 * Class StorageLock
 * @package kalanis\kw_locks\Methods
 */
class StorageLock implements IPassedKey
{
    use TLang;

    protected IStorage $storage;
    protected string $specialKey = '';
    protected string $checkContent = '';

    public function __construct(IStorage $storage, ?IKLTranslations $lang = null)
    {
        $this->storage = $storage;
        $this->setKlLang($lang);
    }

    public function __destruct()
    {
        try {
            $this->delete();
        } catch (LockException $ex) {
            // do nothing instead of
            // register_shutdown_function([$this, 'delete']);
        }
    }

    public function setKey(string $key, string $checkContent = ''): void
    {
        $this->specialKey = $key;
        $this->checkContent = empty($checkContent) ? strval(getmypid()) : $checkContent ;
    }

    public function has(): bool
    {
        try {
            if (!$this->storage->exists($this->specialKey)) {
                return false;
            }
            if ($this->checkContent == strval($this->storage->read($this->specialKey))) {
                return true;
            }
            throw new LockException($this->getKlLang()->iklLockedByOther());
        } catch (StorageException $ex) {
            throw new LockException($this->getKlLang()->iklProblemWithStorage(), $ex->getCode(), $ex);
        }
    }

    public function create(bool $force = false): bool
    {
        if (!$force && $this->has()) {
            return false;
        }
        try {
            $result = $this->storage->write($this->specialKey, $this->checkContent);
            return $result;
        } catch (StorageException $ex) {
            throw new LockException($this->getKlLang()->iklProblemWithStorage(), $ex->getCode(), $ex);
        }
    }

    public function delete(bool $force = false): bool
    {
        if (!$force && !$this->has()) {
            return true;
        }
        try {
            return $this->storage->remove($this->specialKey);
        } catch (StorageException $ex) {
            throw new LockException($this->getKlLang()->iklProblemWithStorage(), $ex->getCode(), $ex);
        }
    }
}
