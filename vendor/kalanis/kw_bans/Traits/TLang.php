<?php

namespace kalanis\kw_bans\Traits;


use kalanis\kw_bans\Interfaces\IKBTranslations;
use kalanis\kw_bans\Translations;


trait TLang
{
    protected ?IKBTranslations $iKbLang = null;

    protected function setIKbLang(?IKBTranslations $iKbLang = null): void
    {
        $this->iKbLang = $iKbLang;
    }

    protected function getIKbLang(): IKBTranslations
    {
        if (empty($this->iKbLang)) {
            $this->iKbLang = new Translations();
        }
        return $this->iKbLang;
    }
}
