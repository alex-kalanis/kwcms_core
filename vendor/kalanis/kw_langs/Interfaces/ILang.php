<?php

namespace kalanis\kw_langs\Interfaces;


/**
 * Class ILang
 * @package kalanis\kw_langs\Interfaces
 * Translation available in class
 */
interface ILang
{
    /**
     * @return string[] translations array
     */
    public function getTranslations(): array;
}
