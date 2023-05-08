<?php

namespace kalanis\kw_user_paths\Interfaces;


/**
 * Interface IUPTranslations
 * @package kalanis\kw_user_paths\Interfaces
 * Translations
 */
interface IUPTranslations
{
    public function upUserNameIsShort(): string;

    public function upUserNameContainsChars(): string;

    public function upUserNameNotDefined(): string;

    public function upCannotDetermineUserDir(): string;

    public function upCannotCreateUserDir(): string;

    public function upCannotGetFullPaths(): string;
}
