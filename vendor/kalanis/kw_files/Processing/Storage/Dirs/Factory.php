<?php

namespace kalanis\kw_files\Processing\Storage\Dirs;


use kalanis\kw_files\Interfaces\IFLTranslations;
use kalanis\kw_storage\Interfaces;


/**
 * Class Factory
 * @package kalanis\kw_files\Processing\Storage\Dirs
 * Process dirs in storages - get correct one
 */
class Factory
{
    public function getClass(Interfaces\IStorage $storage, ?IFLTranslations $lang = null): ADirs
    {
        if ($storage instanceof Interfaces\IPassDirs) {
            if ($storage->isFlat()) {
                return new CanDirFlat($storage);
            } else {
                return new CanDirRecursive($storage, $lang);
            }
        } else {
            return new Basic($storage, $lang);
        }
    }
}
