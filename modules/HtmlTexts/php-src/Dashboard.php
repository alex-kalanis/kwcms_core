<?php

namespace KWCMS\modules\HtmlTexts;


use KWCMS\modules\Texts;


/**
 * Class Dashboard
 * @package KWCMS\modules\HtmlTexts
 * Site's text content - list available files in directory
 */
class Dashboard extends Texts\Dashboard
{
    use Lib\TModuleTemplate;

    protected function getParams(): Texts\Lib\Params
    {
        return new Lib\Params();
    }

    protected function getTargetEdit(): string
    {
        return 'html-texts/edit';
    }
}
