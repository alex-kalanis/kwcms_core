<?php

namespace kalanis\kw_semaphore\Interfaces;


/**
 * Interface ISMTranslations
 * @package kalanis\kw_semaphore\Interfaces
 * Translations
 */
interface ISMTranslations
{
    public function smCannotOpenSemaphore(): string;

    public function smCannotSaveSemaphore(): string;

    public function smCannotGetSemaphoreClass(): string;
}
