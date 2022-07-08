<?php

namespace kalanis\kw_files\Interfaces;


/**
 * Interface ITypes
 * @package kalanis\kw_files\Interfaces
 * Just constants
 */
interface ITypes
{
    const TYPE_DIR = 'dir';
    const TYPE_FILE = 'file';
    // skip rest - link, pipe, socket, ...
}
