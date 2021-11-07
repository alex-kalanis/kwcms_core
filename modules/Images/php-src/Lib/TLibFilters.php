<?php

namespace KWCMS\modules\Images\Lib;


use SplFileInfo;


/**
 * Trait TLibFilters
 * @package KWCMS\modules\Images\Lib
 * Filters
 */
trait TLibFilters
{
    public function filterDirs(SplFileInfo $info): bool
    {
        return $info->isDir();
    }
}
