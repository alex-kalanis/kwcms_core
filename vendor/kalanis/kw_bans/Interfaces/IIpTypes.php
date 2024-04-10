<?php

namespace kalanis\kw_bans\Interfaces;


/**
 * Interface IIpTypes
 * @package kalanis\kw_bans\Interfaces
 */
interface IIpTypes
{
    public const TYPE_NONE = 0;
    public const TYPE_NAME = 1;
    public const TYPE_BASIC = 2;
    public const TYPE_IP_4 = 3;
    public const TYPE_IP_6 = 4;

    public const MASK_SEPARATOR = '/';
}
