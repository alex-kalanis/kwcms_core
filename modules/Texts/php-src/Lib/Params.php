<?php

namespace KWCMS\modules\Texts\Lib;


use kalanis\kw_files\Node;
use kalanis\kw_paths\Stuff;


/**
 * Class Params
 * @package KWCMS\modules\Texts\Lib
 * Extra params for selecting files
 */
class Params
{
    public function filterFiles(Node $info): bool
    {
        if (empty($info->getPath())) {
            // root
            return true;
        }
        $path = $info->getPath();
        $file = end($path);
        $ext = Stuff::fileExt(strval($file));
        return $info->isFile() && in_array($ext, $this->filteredTypes());
    }

    public function filteredTypes(): array
    {
        return ['htm', 'html', 'xhtm', 'xhtml', 'txt', 'mkd', 'md', 'ini', 'inf', ];
    }
}
