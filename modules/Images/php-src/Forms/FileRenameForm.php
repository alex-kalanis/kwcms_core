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
 * @property Controls\Submit saveDir
 * @property Controls\Reset resetDir
 */
class FileRenameForm extends Form
{
    public function composeForm(): self
    {
        $this->setMethod(IEntry::SOURCE_POST);
        $this->addText('newName', Lang::get('menu.current_dir'));
        $this->addSubmit('saveDir', Lang::get('dashboard.button_set'));
        $this->addReset('resetDir', Lang::get('dashboard.button_reset'));
        return $this;
    }
}
