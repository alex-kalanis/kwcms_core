<?php

namespace KWCMS\modules\Transcode\Templates;


use kalanis\kw_templates\ATemplate;
use kalanis\kw_templates\Template\TInputs;


/**
 * Class AddButtonTemplate
 * @package KWCMS\modules\Transcode\Templates
 */
class AddButtonTemplate extends ATemplate
{
    use TInputs;

    public function loadTemplate(): string
    {
        return '<span class="select-button add-button" data-content=\'{ESC_LETTER}\'>{LETTER}</span>';
    }
}
