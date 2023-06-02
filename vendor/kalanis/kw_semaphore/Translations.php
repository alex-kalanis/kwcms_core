<?php

namespace kalanis\kw_semaphore;


use kalanis\kw_semaphore\Interfaces\ISMTranslations;


/**
 * Class Translations
 * @package kalanis\kw_semaphore
 */
class Translations implements ISMTranslations
{
    public function mnCannotOpenSemaphore(): string
    {
        return 'Cannot open semaphore data';
    }

    public function mnCannotSaveSemaphore(): string
    {
        return 'Cannot save semaphore data';
    }

    public function mnCannotGetSemaphoreClass(): string
    {
        return 'Cannot determine semaphore class';
    }
}
