<?php

namespace kalanis\kw_storage\Traits;


use kalanis\kw_storage\Interfaces\IStTranslations;
use kalanis\kw_storage\Translations;


/**
 * Trait TLang
 * @package kalanis\kw_storage\Traits
 * Translations
 */
trait TLang
{
    protected ?IStTranslations $stLang = null;

    public function setStLang(?IStTranslations $lang = null): self
    {
        $this->stLang = $lang;
        return $this;
    }

    public function getStLang(): IStTranslations
    {
        if (empty($this->stLang)) {
            $this->stLang = new Translations();
        }
        return $this->stLang;
    }
}
