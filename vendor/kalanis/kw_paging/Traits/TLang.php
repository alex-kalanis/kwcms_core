<?php

namespace kalanis\kw_paging\Traits;


use kalanis\kw_paging\Interfaces\IPGTranslations;
use kalanis\kw_paging\Translations;


/**
 * Trait TLang
 * @package kalanis\kw_paging\Render\SimplifiedPager
 * Trait for render simple helping text about
 */
trait TLang
{
    protected ?IPGTranslations $kpgLang = null;

    public function setKpgLang(?IPGTranslations $lang): void
    {
        $this->kpgLang = $lang;
    }

    public function getKpgLang(): IPGTranslations
    {
        if (empty($this->kpgLang)) {
            $this->kpgLang = new Translations();
        }
        return $this->kpgLang;
    }
}
