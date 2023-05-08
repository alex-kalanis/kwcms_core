<?php

namespace kalanis\kw_mime\Check\Traits;


use kalanis\kw_mime\Interfaces\IMiTranslations;
use kalanis\kw_mime\Translations;


trait TLang
{
    /** @var IMiTranslations|null */
    protected $miLang = null;

    public function setMiLang(IMiTranslations $lang = null): void
    {
        $this->miLang = $lang;
    }

    public function getMiLang(): IMiTranslations
    {
        if (empty($this->miLang)) {
            $this->miLang = new Translations();
        }
        return $this->miLang;
    }
}
