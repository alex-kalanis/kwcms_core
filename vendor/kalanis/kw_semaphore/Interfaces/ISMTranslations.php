<?php

namespace kalanis\kw_semaphore\Interfaces;


/**
 * Interface ISMTranslations
 * @package kalanis\kw_semaphore\Interfaces
 * Translations
 */
interface ISMTranslations
{
    public function mnCannotOpenSemaphore(): string;

    public function mnCannotSaveSemaphore(): string;

    public function mnCannotGetSemaphoreClass(): string;
}
