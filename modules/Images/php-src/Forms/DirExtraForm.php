<?php

namespace KWCMS\modules\Images\Forms;


use kalanis\kw_forms\Controls;
use kalanis\kw_forms\Form;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_langs\Lang;


/**
 * Class DirExtraForm
 * @package KWCMS\modules\Images\Forms
 * Set dir as usable for extra data
 * @property Controls\Submit saveDir
 */
class DirExtraForm extends Form
{
    public function composeForm(): self
    {
        $this->setMethod(IEntry::SOURCE_POST);
        $this->addSubmit('saveDir', Lang::get('dashboard.button_set'));
        return $this;
    }
}
