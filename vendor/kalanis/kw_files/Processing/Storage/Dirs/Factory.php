<?php

namespace kalanis\kw_files\Processing\Storage\Dirs;


use kalanis\kw_files\Interfaces\IFLTranslations;
use kalanis\kw_storage\Interfaces\IPassDirs;
use kalanis\kw_storage\Interfaces\IStorage;


/**
 * Class Factory
 * @package kalanis\kw_files\Processing\Storage\Dirs
 * Process dirs in storages - get correct one
 */
class Factory
{
    public function getClass(IStorage $storage, ?IFLTranslations $lang = null): ADirs
    {
        if ($storage instanceof IPassDirs) {
            return new CanDir($storage, $lang);
        } else {
            return new Basic($storage, $lang);
        }
    }
}
