<?php

namespace kalanis\kw_forums\Interfaces;


/**
 * Interface ITargets
 * @package kalanis\kw_forums\Interfaces
 */
interface ITargets
{
    const IS_ARCHIVED = false;
    const IS_ALIVE = true;
    const LISTING_THEMAS = 0;
    const LISTING_TOPIC = 1;
    const FORM_SEND_ALL = 0;
    const FORM_SEND_REGISTERED = 1;
    const FORM_SEND_NOBODY = 2;
}
