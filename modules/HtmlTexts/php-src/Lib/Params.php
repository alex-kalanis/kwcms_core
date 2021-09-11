<?php

namespace KWCMS\modules\HtmlTexts\Lib;


/**
 * Class Params
 * @package KWCMS\modules\HtmlTexts\Lib
 * Extra params for selecting files
 */
class Params extends \KWCMS\modules\Texts\Lib\Params
{
    public function filteredTypes(): array
    {
        return ['htm', 'html', 'xhtm', 'xhtml'];
    }
}
