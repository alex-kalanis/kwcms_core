<?php

namespace KWCMS\modules\Images\Forms;


use kalanis\kw_forms\Controls;
use kalanis\kw_forms\Form;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_langs\Lang;


/**
 * Class FileThumbForm
 * @package KWCMS\modules\Images\Forms
 * Set files's thumb as primary for the whole gallery or regenerate it
 * @property Controls\Submit saveFile
 */
class FileThumbForm extends Form
{
    public function composeForm(string $targetLink): self
    {
        $this->setMethod(IEntry::SOURCE_POST);
        $this->setAttribute('action', $targetLink);
        $this->addSubmit('selectFile', Lang::get('dashboard.button_set'));
        return $this;
    }
}
