<?php

namespace kalanis\kw_images\Traits;


use kalanis\kw_images\Interfaces\IIMTranslations;
use kalanis\kw_images\Translations;


/**
 * Trait TLang
 * @package kalanis\kw_images\Traits
 * Process translations
 */
trait TLang
{
    /** @var IIMTranslations|null */
    protected $imLang = null;

    public function setImLang(?IIMTranslations $lang = null): void
    {
        $this->imLang = $lang;
    }

    public function getImLang(): IIMTranslations
    {
        if (empty($this->imLang)) {
            $this->imLang = new Translations();
        }
        return $this->imLang;
    }
}
