<?php

namespace kalanis\kw_bans\Interfaces;


/**
 * Interface IIpTypes
 * @package kalanis\kw_bans\Interfaces
 */
interface IIpTypes
{
    const TYPE_NONE = 0;
    const TYPE_NAME = 1;
    const TYPE_BASIC = 2;
    const TYPE_IP_4 = 3;
    const TYPE_IP_6 = 4;

    const MASK_SEPARATOR = '/';
}
