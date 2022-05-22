<?php

namespace kalanis\kw_bans\Bans;


use kalanis\kw_bans\Interfaces\IKBTranslations;
use kalanis\kw_bans\Ip;


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

    protected function setLang(IKBTranslations $lang): void
    {
        $this->lang = $lang;
    }

    protected function getBasicIp(): Ip
    {
        return $this->basicIp;
    }

    protected function getLang(): IKBTranslations
    {
        return $this->lang;
    }
}
