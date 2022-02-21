<?php

namespace kalanis\kw_menu\Semaphore;


use kalanis\kw_menu\Interfaces\IMNTranslations;
use kalanis\kw_menu\Interfaces\ISemaphore;
use kalanis\kw_menu\MenuException;
use kalanis\kw_menu\Translations;
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
    /** @var IMNTranslations */
    protected $lang = null;

    public function __construct(string $rootPath, ?IMNTranslations $lang = null)
    {
        $this->rootPath = Stuff::removeEndingSlash($rootPath) . static::EXT_SEMAPHORE;
        $this->lang = $lang ?: new Translations();
    }

    public function want(): bool
    {
        if (false === @file_put_contents($this->rootPath, 'RELOAD')) {
            throw new MenuException($this->lang->mnCannotSaveSemaphore());
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
