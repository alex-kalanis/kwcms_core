<?php

namespace KWCMS\modules\Admin\Templates;


use kalanis\kw_langs\Lang;
use kalanis\kw_paging\Render\SimplifiedPager\Pager;


/**
 * Class PagerTemplate
 * @package KWCMS\modules\Admin\Templates
 */
class PagerTemplate extends Pager
{
    protected function templatePath(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'style' . DIRECTORY_SEPARATOR . 'Pager.html';
    }

    protected function getHelpingText(): string
    {
        return Lang::get('pager.results');
    }
}
