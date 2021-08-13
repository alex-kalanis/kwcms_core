<?php

namespace kalanis\kw_bans\Bans;


use kalanis\kw_bans\Interfaces\IIpTypes;


class IP4 extends AIP
{
    protected $type = IIpTypes::TYPE_IP_4;
    protected $blocks = 4;
    protected $delimiter = '.';
    protected $bitsInBlock = 8;

    protected function toNumber(string $value): int
    {
        return intval($value);
    }
}
