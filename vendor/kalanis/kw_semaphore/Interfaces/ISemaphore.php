<?php

namespace kalanis\kw_semaphore\Interfaces;


use kalanis\kw_semaphore\SemaphoreException;


/**
 * Interface ICaching
 * @package kalanis\kw_semaphore\Interfaces
 * Semaphore to tell when reload menu cache
 * Work with directories or other storage devices
 */
interface ISemaphore
{
    const TEXT_SEMAPHORE = 'RELOAD';
    const EXT_SEMAPHORE = '.reload';

    /**
     * We want/mark that thing
     * @return bool
     * @throws SemaphoreException
     */
    public function want(): bool;

    /**
     * Is that thing wanted/marker
     * @return bool
     * @throws SemaphoreException
     */
    public function has(): bool;

    /**
     * Remove want/mark
     * @return bool
     * @throws SemaphoreException
     */
    public function remove(): bool;
}
