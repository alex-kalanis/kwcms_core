<?php

namespace KWCMS\modules\Images\Forms;


use kalanis\kw_forms\Controls;
use kalanis\kw_forms\Form;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_langs\Lang;
use kalanis\kw_tree\Controls\DirSelect;
use kalanis\kw_tree\FileNode;


/**
 * Class FileActionForm
 * @package KWCMS\modules\Images\Forms
 * Copy/move current file into path
 * @property DirSelect where
 * @property Controls\Submit saveDesc
 * @property Controls\Reset resetDesc
 */
class FileActionForm extends Form
{
    public function composeForm(FileNode $targetTree, string $targetLink): self
    {
        $this->setMethod(IEntry::SOURCE_POST);
        $this->setAttribute('action', $targetLink);
        $select = new DirSelect();
        $select->set('where', '', Lang::get('files.dir.select'), $targetTree);
        $this->addControl($select);
        $this->addSubmit('saveFile', Lang::get('dashboard.button_set'));
        $this->addReset('resetFile', Lang::get('dashboard.button_reset'));
        return $this;
    }
}
