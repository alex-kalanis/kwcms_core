<?php

namespace kalanis\kw_bans\Bans;


use kalanis\kw_bans\Interfaces\IIpTypes;


class IP6 extends AIP
{
    protected $type = IIpTypes::TYPE_IP_6;
    protected $blocks = 8;
    protected $delimiter = ':';
    protected $bitsInBlock = 16;

    protected function toNumber(string $value): int
    {
        return intval(hexdec($value));
    }
}
