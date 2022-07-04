<?php

namespace kalanis\kw_files\Processing\Storage\Dirs;


use kalanis\kw_storage\Interfaces\IPassDirs;
use kalanis\kw_storage\Interfaces\IStorage;


/**
 * Class Factory
 * @package kalanis\kw_files\Processing\Storage\Dirs
 * Process dirs in storages - get correct one
 */
class Factory
{
    public function getClass(IStorage $storage): ADirs
    {
        if ($storage instanceof IPassDirs) {
            return new CanDir($storage);
        } else {
            return new Basic($storage);
        }
    }
}
