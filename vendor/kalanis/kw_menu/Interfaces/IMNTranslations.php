<?php

namespace kalanis\kw_menu\Interfaces;


/**
 * Interface IMNTranslations
 * @package kalanis\kw_menu\Interfaces
 * Translations
 */
interface IMNTranslations
{
    public function mnCannotOpen(): string;

    public function mnCannotSave(): string;

    public function mnItemNotFound(string $item): string;

    public function mnProblematicData(): string;
}
