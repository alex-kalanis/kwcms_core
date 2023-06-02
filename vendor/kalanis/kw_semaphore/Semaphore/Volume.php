<?php

namespace kalanis\kw_semaphore\Semaphore;


use kalanis\kw_paths\Stuff;
use kalanis\kw_semaphore\Interfaces\ISemaphore;
use kalanis\kw_semaphore\Interfaces\ISMTranslations;
use kalanis\kw_semaphore\SemaphoreException;
use kalanis\kw_semaphore\Traits\TLang;


/**
 * Class Volume
 * @package kalanis\kw_semaphore\Semaphore
 * Data source for semaphore is volume
 */
class Volume implements ISemaphore
{
    use TLang;

    /** @var string path to menu dir */
    protected $rootPath = '';

    public function __construct(string $rootPath, ?ISMTranslations $lang = null)
    {
        $this->rootPath = Stuff::removeEndingSlash($rootPath) . static::EXT_SEMAPHORE;
        $this->setSmLang($lang);
    }

    public function want(): bool
    {
        if (false === @file_put_contents($this->rootPath, static::TEXT_SEMAPHORE)) {
            throw new SemaphoreException($this->getSmLang()->mnCannotSaveSemaphore());
        }
        return true;
    }

    public function has(): bool
    {
        return is_file($this->rootPath);
    }

    public function remove(): bool
    {
        if (file_exists($this->rootPath) && (false === @unlink($this->rootPath))) {
            throw new SemaphoreException($this->getSmLang()->mnCannotOpenSemaphore());
        }
        return true;
    }
}
