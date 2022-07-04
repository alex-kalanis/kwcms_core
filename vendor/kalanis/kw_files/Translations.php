<?php

namespace kalanis\kw_files;


use kalanis\kw_files\Interfaces\IFLTranslations;


/**
 * Class Translations
 * @package kalanis\kw_files
 * Translations
 */
class Translations implements IFLTranslations
{
    public function flCannotLoadFile(string $fileName): string
    {
        return 'Cannot load wanted file.';
    }

    public function flCannotSaveFile(string $fileName): string
    {
        return 'Cannot save wanted file.';
    }
}
