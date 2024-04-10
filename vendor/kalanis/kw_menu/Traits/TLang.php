<?php

namespace kalanis\kw_menu\Traits;


use kalanis\kw_menu\Interfaces\IMNTranslations;
use kalanis\kw_menu\Translations;


/**
 * Trait TEscape
 * @package kalanis\kw_menu\Traits
 * Translations
 */
trait TLang
{
    protected ?IMNTranslations $mnLang = null;

    public function setMnLang(?IMNTranslations $lang = null): self
    {
        $this->mnLang = $lang;
        return $this;
    }

    public function getMnLang(): IMNTranslations
    {
        if (empty($this->mnLang)) {
            $this->mnLang = new Translations();
        }
        return $this->mnLang;
    }
}
