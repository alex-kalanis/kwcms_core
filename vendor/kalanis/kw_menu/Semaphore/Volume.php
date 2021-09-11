<?php

namespace kalanis\kw_menu\Semaphore;


use kalanis\kw_langs\Lang;
use kalanis\kw_menu\Interfaces\ISemaphore;
use kalanis\kw_menu\MenuException;
use kalanis\kw_paths\Stuff;


/**
 * Class Volume
 * @package kalanis\kw_menu\Semaphore
 * Data source for semaphore is volume
 */
class Volume implements ISemaphore
{
    /** @var string path to menu dir */
    protected $rootPath = '';

    public function __construct(string $rootPath)
    {
        $this->rootPath = Stuff::removeEndingSlash($rootPath) . static::EXT_SEMAPHORE;
    }

    public function want(): bool
    {
        if (false === @file_put_contents($this->rootPath, 'RELOAD')) {
            throw new MenuException(Lang::get('menu.error.cannot_save'));
        }
        return true;
    }

    public function has(): bool
    {
        return is_file($this->rootPath);
    }

    public function remove(): bool
    {
        return @unlink($this->rootPath);
    }
}
