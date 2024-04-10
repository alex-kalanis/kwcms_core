<?php

namespace kalanis\kw_files\Traits;


use kalanis\kw_files\Interfaces\IFLTranslations;
use kalanis\kw_files\Translations;


/**
 * Trait TLang
 * @package kalanis\kw_files\Traits
 * Translations trait
 */
trait TLang
{
    protected ?IFLTranslations $flLang = null;

    public function setFlLang(?IFLTranslations $flLang = null): void
    {
        $this->flLang = $flLang;
    }

    public function getFlLang(): IFLTranslations
    {
        if (empty($this->flLang)) {
            $this->flLang = new Translations();
        }
        return $this->flLang;
    }
}
