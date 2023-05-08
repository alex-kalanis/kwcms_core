<?php

namespace kalanis\kw_user_paths;


use kalanis\kw_user_paths\Interfaces\IUPTranslations;


/**
 * Class Translations
 * @package kalanis\kw_user_paths
 * Translations
 */
class Translations implements IUPTranslations
{
    public function upUserNameIsShort(): string
    {
        return 'Username is short!';
    }

    public function upUserNameContainsChars(): string
    {
        return 'Username contains unsupported characters!';
    }

    public function upUserNameNotDefined(): string
    {
        return 'Necessary user name is not defined!';
    }

    public function upCannotDetermineUserDir(): string
    {
        return 'Cannot determine user dir!';
    }

    public function upCannotCreateUserDir(): string
    {
        return 'Cannot create user dir!';
    }

    public function upCannotGetFullPaths(): string
    {
        return 'Cannot get full path class!';
    }
}
