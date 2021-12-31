<?php

namespace KWCMS\modules\Transcode\Templates;


use kalanis\kw_templates\ATemplate;
use kalanis\kw_templates\Template\TInputs;


/**
 * Class RmButtonTemplate
 * @package KWCMS\modules\Transcode\Templates
 */
class RmButtonTemplate extends ATemplate
{
    use TInputs;

    public function loadTemplate(): string
    {
        return '<span class="select-button remove-button">{LETTER}</span';
    }
}
