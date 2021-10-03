<?php

namespace KWCMS\modules\Images\Forms;


use kalanis\kw_forms\Controls;
use kalanis\kw_forms\Form;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_langs\Lang;
use kalanis\kw_tree\Controls\DirSelect;
use kalanis\kw_tree\FileNode;


/**
 * Class DirNewForm
 * @package KWCMS\modules\Images\Forms
 * New dir with extras
 * @property Controls\Text name
 * @property DirSelect where
 * @property Controls\Checkbox into
 * @property Controls\Submit saveDir
 * @property Controls\Reset resetDir
 */
class DirNewForm extends Form
{
    public function composeForm(FileNode $tree): self
    {
        $this->setMethod(IEntry::SOURCE_POST);
        $select = new DirSelect();
        $select->set('where', '', Lang::get('files.dir.select'), $tree);
        $this->addControl($select);
        $this->addText('name', Lang::get('menu.current_dir'));
        $this->addCheckbox('into', Lang::get('menu.current_dir'));
        $this->addSubmit('saveDir', Lang::get('dashboard.button_set'));
        $this->addReset('resetDir', Lang::get('dashboard.button_reset'));
        return $this;
    }
}