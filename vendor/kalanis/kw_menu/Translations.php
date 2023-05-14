<?php

namespace kalanis\kw_menu;


use kalanis\kw_menu\Interfaces\IMNTranslations;


/**
 * Class Translations
 * @package kalanis\kw_menu
 */
class Translations implements IMNTranslations
{
    public function mnCannotOpen(): string
    {
        return 'Cannot open menu metadata';
    }

    public function mnCannotSave(): string
    {
        return 'Cannot write menu metadata';
    }

    public function mnItemNotFound(string $item): string
    {
        return sprintf('Item for file *%s* not found', $item);
    }

    public function mnProblematicData(): string
    {
        return 'You post problematic data!';
    }

    public function mnNoAvailableEntrySource(): string
    {
        return 'No available entry source for set params!';
    }

    public function mnNoAvailableMetaSource(): string
    {
        return 'No available meta data source for set params!';
    }
}
