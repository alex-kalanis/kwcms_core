<?php

namespace kalanis\kw_bans\Traits;


use kalanis\kw_bans\Ip;


trait TIp
{
    /** @var Ip|null */
    protected $basicIp = null;

    protected function setBasicIp(?Ip $ip = null): void
    {
        $this->basicIp = $ip;
    }

    protected function getBasicIp(): Ip
    {
        if (empty($this->basicIp)) {
            $this->basicIp = new Ip();
        }
        return $this->basicIp;
    }
}
