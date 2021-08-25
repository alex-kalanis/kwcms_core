<?php

namespace KWCMS\modules\Texts\Lib;


use SplFileInfo;


/**
 * Class Params
 * @package KWCMS\modules\Texts\Lib
 * Extra params for selecting files
 */
class Params
{
    public function filterFiles(SplFileInfo $info): bool
    {
        return in_array($info->getExtension(), ['htm', 'html', 'txt', 'mkd', 'ini', 'inf',]);
    }
}
