<?php

namespace kalanis\kw_menu\Interfaces;


use kalanis\kw_menu\MenuException;


/**
 * Interface ICaching
 * @package kalanis\kw_menu\Interfaces
 * Semaphore to tell when reload menu cache
 * Work with directories or other storage devices
 */
interface ISemaphore
{
    const EXT_SEMAPHORE = '.reload';

    /**
     * @return bool
     * @throws MenuException
     */
    public function want(): bool;

    /**
     * @return bool
     * @throws MenuException
     */
    public function has(): bool;

    /**
     * @return bool
     * @throws MenuException
     */
    public function remove(): bool;
}
