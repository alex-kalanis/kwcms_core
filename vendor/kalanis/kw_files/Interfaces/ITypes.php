<?php

namespace kalanis\kw_files\Interfaces;


/**
 * Interface ITypes
 * @package kalanis\kw_files\Interfaces
 * Just constants; probably will be enum in later versions
 */
interface ITypes
{
    /** @link https://www.php.net/manual/en/splfileinfo.gettype.php */
    const TYPE_DIR = 'dir';
    const TYPE_FILE = 'file';
    const TYPE_LINK = 'link';
    const TYPE_BLOCK = 'block';
    const TYPE_FIFO = 'fifo';
    const TYPE_CHAR = 'char';
    const TYPE_SOCKET = 'socket';
    const TYPE_UNKNOWN = 'unknown';
}
