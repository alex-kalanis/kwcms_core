<?php

namespace KWCMS\modules\Images\Lib;


use kalanis\kw_files\Node;
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

    public function filterDirNodes(Node $info): bool
    {
        return $info->isDir();
    }
}
