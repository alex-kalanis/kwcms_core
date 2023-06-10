<?php

namespace KWCMS\modules\Menu\Lib;


use kalanis\kw_langs\Lang;
use kalanis\kw_menu\Interfaces\IMNTranslations;
use kalanis\kw_semaphore\Interfaces\ISMTranslations;


/**
 * Class Translations
 * @package KWCMS\modules\Menu\Lib
 */
class Translations implements IMNTranslations, ISMTranslations
{
    public function mnCannotOpen(): string
    {
        return Lang::get('menu.error.cannot_open');
    }

    public function mnCannotSave(): string
    {
        return Lang::get('menu.error.cannot_save');
    }

    public function mnCannotOpenSemaphore(): string
    {
        return Lang::get('menu.error.cannot_open');
    }

    public function mnCannotSaveSemaphore(): string
    {
        return Lang::get('menu.error.cannot_save');
    }

    public function mnItemNotFound(string $item): string
    {
        return Lang::get('menu.error.item_not_found', $item);
    }

    public function mnProblematicData(): string
    {
        return Lang::get('menu.error.problematic_data');
    }

    public function mnNoAvailableEntrySource(): string
    {
        return Lang::get('menu.error.no_entry_source');
    }

    public function mnNoAvailableMetaSource(): string
    {
        return Lang::get('menu.error.no_meta_source');
    }

    public function mnCannotGetSemaphoreClass(): string
    {
        return Lang::get('menu.error.no_semaphore');
    }
}
