<?php

namespace KWCMS\modules\Images\Forms;


use kalanis\kw_forms\Controls;
use kalanis\kw_forms\Form;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_langs\Lang;


/**
 * Class DescForm
 * @package KWCMS\modules\Images\Forms
 * Edit description of dir/file
 * @property Controls\Text description
 * @property Controls\Submit saveDesc
 * @property Controls\Reset resetDesc
 */
class DescForm extends Form
{
    public function composeForm(string $currentDesc, string $targetLink): self
    {
        $this->setMethod(IEntry::SOURCE_POST);
        $this->setAttribute('action', $targetLink);
        $this->addText('description', Lang::get('images.current_dir_desc'), $currentDesc);
        $this->addSubmit('saveDesc', Lang::get('dashboard.button_set'));
        $this->addReset('resetDesc', Lang::get('dashboard.button_reset'));
        return $this;
    }
}
