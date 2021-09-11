<?php

namespace KWCMS\modules\MdTexts;


use KWCMS\modules\Texts;


/**
 * Class Dashboard
 * @package KWCMS\modules\MdTexts
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
        return 'md-texts/edit';
    }
}
