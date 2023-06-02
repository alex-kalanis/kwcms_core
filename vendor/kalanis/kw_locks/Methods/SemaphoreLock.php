<?php

namespace kalanis\kw_locks\Methods;


use kalanis\kw_locks\Interfaces\IKLTranslations;
use kalanis\kw_locks\Interfaces\IPassedKey;
use kalanis\kw_locks\LockException;
use kalanis\kw_locks\Traits\TLang;
use kalanis\kw_semaphore\Interfaces\ISemaphore;
use kalanis\kw_semaphore\SemaphoreException;


/**
 * Class SemaphoreLock
 * @package kalanis\kw_locks\Methods
 */
class SemaphoreLock implements IPassedKey
{
    use TLang;

    /** @var ISemaphore */
    protected $semaphore = null;
    /** @var string[] */
    protected $specialKey = [];
    /** @var string */
    protected $checkContent = '';

    public function __construct(ISemaphore $semaphore, ?IKLTranslations $lang = null)
    {
        $this->semaphore = $semaphore;
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
    }

    public function has(): bool
    {
        try {
            return $this->semaphore->has();
        } catch (SemaphoreException $ex) {
            throw new LockException($this->getKlLang()->iklProblemWithStorage(), $ex->getCode(), $ex);
        }
    }

    public function create(bool $force = false): bool
    {
        if (!$force && $this->has()) {
            return false;
        }
        try {
            return $this->semaphore->want();
        } catch (SemaphoreException $ex) {
            throw new LockException($this->getKlLang()->iklProblemWithStorage(), $ex->getCode(), $ex);
        }
    }

    public function delete(bool $force = false): bool
    {
        if (!$force && !$this->has()) {
            return true;
        }
        try {
            return $this->semaphore->remove();
        } catch (SemaphoreException $ex) {
            throw new LockException($this->getKlLang()->iklProblemWithStorage(), $ex->getCode(), $ex);
        }
    }
}
