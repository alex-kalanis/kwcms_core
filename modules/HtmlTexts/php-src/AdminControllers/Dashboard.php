<?php

namespace KWCMS\modules\HtmlTexts\AdminControllers;


use KWCMS\modules\Texts;
use KWCMS\modules\HtmlTexts\Lib;


/**
 * Class Dashboard
 * @package KWCMS\modules\HtmlTexts\AdminControllers
 * Site's text content - list available files in directory
 */
class Dashboard extends Texts\AdminControllers\Dashboard
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
