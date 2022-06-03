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
     * @param string $lang
     * @return $this
     */
    public function setLang(string $lang): self;

    /**
     * @return string[] translations array
     */
    public function getTranslations(): array;
}
