<?php

namespace kalanis\kw_bans\Bans;


use kalanis\kw_bans\Interfaces\IIpTypes;


class IP6 extends AIP
{
    protected int $type = IIpTypes::TYPE_IP_6;
    protected int $blocks = 8;
    protected string $delimiter = ':';
    protected int $bitsInBlock = 16;

    protected function toNumber(string $value): int
    {
        return intval(hexdec($value));
    }
}
