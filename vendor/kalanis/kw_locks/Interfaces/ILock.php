<?php

namespace kalanis\kw_locks\Interfaces;


use kalanis\kw_locks\LockException;


/**
 * Interface ILock
 * @package kalanis\kw_locks\Interfaces
 * Basic lock properties
 */
interface ILock
{
    const LOCK_FILE = '.lock'; # lock file ext

    /**
     * Already has lock
     * @return bool
     * @throws LockException
     */
    public function has(): bool;

    /**
     * Create new one
     * @param bool $force forced creation
     * @return bool
     * @throws LockException
     */
    public function create(bool $force = false): bool;

    /**
     * Remove current one
     * @param bool $force forced removal
     * @return bool
     * @throws LockException
     */
    public function delete(bool $force = false): bool;
}
