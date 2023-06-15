<?php

namespace kalanis\kw_user_paths\Traits;


use kalanis\kw_user_paths\Interfaces\IUPTranslations;
use kalanis\kw_user_paths\Translations;


/**
 * Trait TLang
 * @package kalanis\kw_auth\Traits
 */
trait TLang
{
    /** @var IUPTranslations|null */
    protected $upLang = null;

    public function setUpLang(?IUPTranslations $upLang = null): void
    {
        $this->upLang = $upLang;
    }

    public function getUpLang(): IUPTranslations
    {
        if (empty($this->upLang)) {
            $this->upLang = new Translations();
        }
        return $this->upLang;
    }
}
