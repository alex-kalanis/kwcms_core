<?php

namespace KWCMS\modules\MdTexts\Lib;


/**
 * Class Params
 * @package KWCMS\modules\MdTexts\Lib
 * Extra params for selecting files
 */
class Params extends \KWCMS\modules\Texts\Lib\Params
{
    public function filteredTypes(): array
    {
        return ['mkd', 'md'];
    }
}
