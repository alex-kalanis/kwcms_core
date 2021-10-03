<?php

namespace KWCMS\modules\Images\Forms;


use kalanis\kw_forms\Controls;
use kalanis\kw_forms\Form;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_langs\Lang;


/**
 * Class DirDescForm
 * @package KWCMS\modules\Images\Forms
 * Edit description of dir
 * @property Controls\Text description
 * @property Controls\Submit saveDesc
 * @property Controls\Reset resetDesc
 */
class DirDescForm extends Form
{
    public function composeForm(string $currentDesc): self
    {
        $this->setMethod(IEntry::SOURCE_POST);
        $this->addText('description', Lang::get('menu.current_dir'), $currentDesc);
        $this->addSubmit('saveDesc', Lang::get('dashboard.button_set'));
        $this->addReset('resetDesc', Lang::get('dashboard.button_reset'));
        return $this;
    }
}