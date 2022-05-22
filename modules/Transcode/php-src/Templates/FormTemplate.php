<?php

namespace KWCMS\modules\Transcode\Templates;


use kalanis\kw_templates\ATemplate;
use kalanis\kw_templates\Template\TInputs;


/**
 * Class FormTemplate
 * @package KWCMS\modules\Transcode\Templates
 */
class FormTemplate extends ATemplate
{
    use TInputs;

    public function loadTemplate(): string
    {
        return '{FORM_ITSELF}<br />{FROM_BUTTONS}<br />{TO_BUTTONS}<br />{RM_BUTTON}';
    }
}
