<?php

namespace KWCMS\modules\HtmlTexts;


use KWCMS\modules\Texts;


/**
 * Class Preview
 * @package KWCMS\modules\HtmlTexts
 * Site's text preview - show what will be rendered and saved
 */
class Preview extends Texts\Preview
{
    use Lib\TModuleTemplate;

    protected function getParams(): Texts\Lib\Params
    {
        return new Lib\Params();
    }
}
