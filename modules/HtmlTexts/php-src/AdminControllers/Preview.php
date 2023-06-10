<?php

namespace KWCMS\modules\HtmlTexts\AdminControllers;


use KWCMS\modules\Texts;
use KWCMS\modules\HtmlTexts\Lib;


/**
 * Class Preview
 * @package KWCMS\modules\HtmlTexts\AdminControllers
 * Site's text preview - show what will be rendered and saved
 */
class Preview extends Texts\AdminControllers\Preview
{
    use Lib\TModuleTemplate;

    protected function getParams(): Texts\Lib\Params
    {
        return new Lib\Params();
    }
}
