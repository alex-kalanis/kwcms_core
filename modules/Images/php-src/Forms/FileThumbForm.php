<?php

namespace KWCMS\modules\Images\Forms;


use kalanis\kw_forms\Controls;
use kalanis\kw_forms\Form;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_langs\Lang;


/**
 * Class FileThumbForm
 * @package KWCMS\modules\Images\Forms
 * Set files's thumb as primary for the whole gallery
 * @property Controls\Submit saveFile
 */
class FileThumbForm extends Form
{
    public function composeForm(): self
    {
        $this->setMethod(IEntry::SOURCE_POST);
        $this->addSubmit('saveFile', Lang::get('dashboard.button_set'));
        return $this;
    }
}
