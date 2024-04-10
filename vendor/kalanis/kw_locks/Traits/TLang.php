<?php

namespace kalanis\kw_locks\Traits;


use kalanis\kw_locks\Interfaces\IKLTranslations;
use kalanis\kw_locks\Translations;


/**
 * Trait TEscape
 * @package kalanis\kw_locks\Traits
 * Translations
 */
trait TLang
{
    protected ?IKLTranslations $klLang = null;

    public function setKlLang(?IKLTranslations $lang = null): self
    {
        $this->klLang = $lang;
        return $this;
    }

    public function getKlLang(): IKLTranslations
    {
        if (empty($this->klLang)) {
            $this->klLang = new Translations();
        }
        return $this->klLang;
    }
}
