<?php

namespace KWCMS\modules\Transcode\Templates;


use kalanis\kw_templates\ATemplate;
use kalanis\kw_templates\Template\TInputs;


/**
 * Class IndexLinkTemplate
 * @package KWCMS\modules\Transcode\Templates
 */
class IndexLinkTemplate extends ATemplate
{
    use TInputs;

    public function loadTemplate(): string
    {
        return '<li><a href="{ADDR}">{NAME}</a></li>';
    }
}
