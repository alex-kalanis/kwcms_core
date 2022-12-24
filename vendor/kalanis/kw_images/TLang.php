<?php

namespace kalanis\kw_images;


use kalanis\kw_images\Interfaces\IIMTranslations;


/**
 * Trait TLang
 * @package kalanis\kw_images
 * Process translations
 */
trait TLang
{
    /** @var IIMTranslations|null */
    protected $lang = null;

    public function setLang(?IIMTranslations $lang): void
    {
        $this->lang = $lang ?: new Translations();
    }

    public function getLang(): IIMTranslations
    {
        return $this->lang ?: new Translations();
    }
}
