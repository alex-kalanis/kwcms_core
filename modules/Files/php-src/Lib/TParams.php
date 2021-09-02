<?php

namespace KWCMS\modules\Files\Lib;


use SplFileInfo;


/**
 * Trait TParams
 * @package KWCMS\modules\Files\Lib
 * Extra params for selecting content
 */
trait TParams
{
    public function filterFiles(SplFileInfo $info): bool
    {
        return $info->isFile();
    }

    public function filterDirs(SplFileInfo $info): bool
    {
        return $info->isDir();
    }
}
