<?php

namespace kalanis\kw_files\Processing\Storage\Dirs;


use kalanis\kw_files\Interfaces\IProcessDirs;
use kalanis\kw_files\Processing\TPathTransform;


/**
 * Class ADirs
 * @package kalanis\kw_files\Processing\Storage\Dirs
 * Process dirs in storages - deffer when you can access them directly or must be a middleman there
 */
abstract class ADirs implements IProcessDirs
{
    use TPathTransform;

    protected function getStorageSeparator(): string
    {
        return DIRECTORY_SEPARATOR;
    }
}
