<?php

namespace kalanis\kw_files\Traits;


use kalanis\kw_files\Interfaces\IFLTranslations;
use kalanis\kw_files\Translations;


/**
 * Trait TLang
 * @package kalanis\kw_files\Processing
 * Translations trait
 */
trait TLang
{
    /** @var IFLTranslations|null */
    protected $lang = null;

    public function setLang(?IFLTranslations $lang = null): void
    {
        $this->lang = $lang;
    }

    public function getLang(): IFLTranslations
    {
        if (empty($this->lang)) {
            $this->lang = new Translations();
        }
        return $this->lang;
    }
}
