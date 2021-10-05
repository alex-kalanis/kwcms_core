<?php

namespace KWCMS\modules\Images\Forms;


use kalanis\kw_forms\Controls;
use kalanis\kw_forms\Form;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_langs\Lang;


/**
 * Class FileDeleteForm
 * @package KWCMS\modules\Images\Forms
 * Delete this file
 * @property Controls\Submit removeFile
 */
class FileDeleteForm extends Form
{
    public function composeForm(string $targetLink): self
    {
        $this->setMethod(IEntry::SOURCE_POST);
        $this->setAttribute('action', $targetLink);
        $this->addSubmit('removeFile', Lang::get('dashboard.button_set'));
        return $this;
    }
}
