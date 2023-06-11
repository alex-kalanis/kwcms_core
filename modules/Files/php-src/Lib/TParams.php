<?php

namespace KWCMS\modules\Files\Lib;


use kalanis\kw_files\Node;


/**
 * Trait TParams
 * @package KWCMS\modules\Files\Lib
 * Extra params for selecting content
 */
trait TParams
{
    public function filterFileTree(\SplFileInfo $info): bool
    {
        return $info->isFile();
    }

    public function filterFilesTree(Node $info): bool
    {
        if (empty($info->getPath())) {
            return true;
        }
        return $info->isFile();
    }

    public function filterFiles(Node $info): bool
    {
        return $info->isFile();
    }

    public function filterDirs(Node $info): bool
    {
        return $info->isDir();
    }
}
