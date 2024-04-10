<?php

namespace kalanis\kw_locks\Methods;


use kalanis\kw_locks\Interfaces\ILock;
use kalanis\kw_locks\LockException;


/**
 * Class MemoryLock
 * @package kalanis\kw_locks\Methods
 * Lock some target
 * Uses low-level volume operations
 */
class MemoryLock implements ILock
{
    protected bool $lock = false;

    public function __destruct()
    {
        try {
            $this->delete();
            // @codeCoverageIgnoreStart
        } catch (LockException $ex) {
            // do nothing instead of
            // register_shutdown_function([$this, 'delete']);
        }
        // @codeCoverageIgnoreEnd
    }

    public function has(): bool
    {
        return $this->lock;
    }

    public function create(bool $force = false): bool
    {
        if ($this->has()) {
            return false;
        }

        $this->lock = true;
        return true;
    }

    public function delete(bool $force = false): bool
    {
        if (!$this->has()) {
            return true;
        }

        $this->lock = false;
        return true;
    }
}
