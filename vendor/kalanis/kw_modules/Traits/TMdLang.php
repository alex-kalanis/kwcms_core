<?php

namespace kalanis\kw_modules\Traits;


use kalanis\kw_modules\Interfaces\IMdTranslations;
use kalanis\kw_modules\Translations;


/**
 * Trait TMdLang
 * @package kalanis\kw_files\Processing
 * Translations trait
 */
trait TMdLang
{
    /** @var IMdTranslations|null */
    protected $mdLang = null;

    public function setMdLang(?IMdTranslations $mdLang = null): void
    {
        $this->mdLang = $mdLang;
    }

    public function getMdLang(): IMdTranslations
    {
        if (empty($this->mdLang)) {
            $this->mdLang = new Translations();
        }
        return $this->mdLang;
    }
}
