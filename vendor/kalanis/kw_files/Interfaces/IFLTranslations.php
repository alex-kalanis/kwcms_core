<?php

namespace kalanis\kw_files\Interfaces;


/**
 * Interface IFLTranslations
 * @package kalanis\kw_files\Interfaces
 * Translations
 */
interface IFLTranslations
{
    public function flCannotLoadFile(string $fileName): string;

    public function flCannotSaveFile(string $fileName): string;
}
