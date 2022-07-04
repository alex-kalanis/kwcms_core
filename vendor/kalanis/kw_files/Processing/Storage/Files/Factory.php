<?php

namespace kalanis\kw_files\Processing\Storage\Files;


use kalanis\kw_storage\Interfaces\IPassDirs;
use kalanis\kw_storage\Interfaces\IStorage;


/**
 * Class Factory
 * @package kalanis\kw_files\Processing\Storage\Files
 * Process files in storages - get correct one
 */
class Factory
{
    public function getClass(IStorage $storage): AFiles
    {
        if ($storage instanceof IPassDirs) {
            return new CanDir($storage);
        } else {
            return new Basic($storage);
        }
    }
}
