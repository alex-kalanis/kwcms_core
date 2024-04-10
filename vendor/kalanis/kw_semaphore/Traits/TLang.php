<?php

namespace kalanis\kw_semaphore\Traits;


use kalanis\kw_semaphore\Interfaces\ISMTranslations;
use kalanis\kw_semaphore\Translations;


/**
 * Trait TLang
 * @package kalanis\kw_semaphore\Traits
 * Translations
 */
trait TLang
{
    protected ?ISMTranslations $smLang = null;

    public function setSmLang(?ISMTranslations $lang = null): self
    {
        $this->smLang = $lang;
        return $this;
    }

    public function getSmLang(): ISMTranslations
    {
        if (empty($this->smLang)) {
            $this->smLang = new Translations();
        }
        return $this->smLang;
    }
}
