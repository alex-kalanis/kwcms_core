<?php

namespace KWCMS\modules\Transcode\Templates;


use kalanis\kw_templates\ATemplate;
use kalanis\kw_templates\Template\TInputs;


/**
 * Class IndexMenuTemplate
 * @package KWCMS\modules\Transcode\Templates
 */
class IndexMenuTemplate extends ATemplate
{
    use TInputs;

    public function loadTemplate(): string
    {
        return '<ul>{LINKS}</ul>';
    }
}
