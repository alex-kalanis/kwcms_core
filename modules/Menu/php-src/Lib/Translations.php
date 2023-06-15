<?php

namespace KWCMS\modules\Menu\Lib;


use kalanis\kw_langs\Lang;
use kalanis\kw_menu\Interfaces\IMNTranslations;
use kalanis\kw_semaphore\Interfaces\ISMTranslations;
use kalanis\kw_user_paths\Interfaces\IUPTranslations;


/**
 * Class Translations
 * @package KWCMS\modules\Menu\Lib
 */
class Translations implements IMNTranslations, ISMTranslations, IUPTranslations
{
    public function mnCannotOpen(): string
    {
        return Lang::get('menu.error.cannot_open');
    }

    public function mnCannotSave(): string
    {
        return Lang::get('menu.error.cannot_save');
    }

    public function smCannotOpenSemaphore(): string
    {
        return Lang::get('menu.error.cannot_open');
    }

    public function smCannotSaveSemaphore(): string
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

    public function smCannotGetSemaphoreClass(): string
    {
        return Lang::get('menu.error.no_semaphore');
    }

    public function upUserNameIsShort(): string
    {
        return Lang::get('chdir.user_dir.username_is_short');
    }

    public function upUserNameContainsChars(): string
    {
        return Lang::get('chdir.user_dir.user_name_contains_chars');
    }

    public function upUserNameNotDefined(): string
    {
        return Lang::get('chdir.user_dir.user_name_not_defined');
    }

    public function upCannotDetermineUserDir(): string
    {
        return Lang::get('chdir.user_dir.cannot_determine_user_dir');
    }

    public function upCannotCreateUserDir(): string
    {
        return Lang::get('chdir.user_dir.cannot_create_user_dir');
    }

    public function upCannotGetFullPaths(): string
    {
        return Lang::get('chdir.user_dir.cannot_get_full_paths');
    }
}
