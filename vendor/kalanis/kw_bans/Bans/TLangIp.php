<?php

namespace kalanis\kw_bans\Bans;


use kalanis\kw_bans\Interfaces\IKBTranslations;
use kalanis\kw_bans\Ip;
use kalanis\kw_bans\Translations;


trait TLangIp
{
    /** @var Ip */
    protected $basicIp = null;
    /** @var IKBTranslations */
    protected $lang = null;

    protected function setBasicIp(Ip $ip): void
    {
        $this->basicIp = $ip;
    }

    protected function setLang(?IKBTranslations $lang = null): void
    {
        $this->lang = $lang;
    }

    protected function getBasicIp(): Ip
    {
        return $this->basicIp;
    }

    protected function getLang(): IKBTranslations
    {
        if (empty($this->lang)) {
            $this->lang = new Translations();
        }
        return $this->lang;
    }
}
