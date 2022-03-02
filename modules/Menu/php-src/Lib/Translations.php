<?php

namespace KWCMS\modules\Menu\Lib;


use kalanis\kw_langs\Lang;
use kalanis\kw_menu\Interfaces\IMNTranslations;


/**
 * Class Translations
 * @package KWCMS\modules\Menu\Lib
 */
class Translations implements IMNTranslations
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
}
