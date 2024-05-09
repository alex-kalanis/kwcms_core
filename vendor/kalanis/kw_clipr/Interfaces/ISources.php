<?php

namespace kalanis\kw_clipr\Interfaces;


/**
 * Interface ISources
 * @package kalanis\kw_clipr\Interfaces
 * Which sources combination are available
 */
interface ISources
{
    public const SOURCE_CLEAR = 'clear';
    public const SOURCE_WEB = 'web';
    public const SOURCE_POSIX = 'lin';
    public const SOURCE_WINDOWS = 'win';

    public const OUTPUT_STD = 'STDOUT';
    public const EXT_PHP = '.php';
}
