<?php

namespace KWCMS\modules\Images\Forms;


use kalanis\kw_forms\Controls;
use kalanis\kw_forms\Form;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_langs\Lang;


/**
 * Class FileRenameForm
 * @package KWCMS\modules\Images\Forms
 * Rename file
 * @property Controls\Text newName
 * @property Controls\Submit saveFile
 * @property Controls\Reset resetFile
 */
class FileRenameForm extends Form
{
    public function composeForm(): self
    {
        $this->setMethod(IEntry::SOURCE_POST);
        $this->addText('newName', Lang::get('menu.current_dir'));
        $this->addSubmit('saveFile', Lang::get('dashboard.button_set'));
        $this->addReset('resetFile', Lang::get('dashboard.button_reset'));
        return $this;
    }
}
