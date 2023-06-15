<?php

namespace KWCMS\modules\Short\Lib;


use kalanis\kw_langs\Lang;
use kalanis\kw_user_paths\Interfaces\IUPTranslations;


/**
 * Class Translations
 * @package KWCMS\modules\Short\Lib
 */
class Translations implements IUPTranslations
{
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
