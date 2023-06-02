<?php

namespace kalanis\kw_semaphore\Traits;


use kalanis\kw_semaphore\Interfaces\ISMTranslations;
use kalanis\kw_semaphore\Translations;


/**
 * Trait TEscape
 * @package kalanis\kw_semaphore\Traits
 * Translations
 */
trait TLang
{
    /** @var ISMTranslations|null */
    protected $smLang = null;

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
