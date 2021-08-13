<?php

namespace KWCMS\modules\Admin\Forms;


use kalanis\kw_forms\Controls\Submit;
use kalanis\kw_forms\Form;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_tree\Controls;
use kalanis\kw_tree\FileNode;


/**
 * Class ChDirForm
 * @package KWCMS\modules\Admin\Forms
 * Admin change directory form
 * @property Controls\DirRadio dir
 * @property Submit changeDir
 */
class ChDirForm extends Form
{
    public function composeForm(string $defaultWhere, ?FileNode $tree): self
    {
        $this->setMethod(IEntry::SOURCE_POST);
        $radios = new Controls\DirRadio();
        $radios->set('dir', $defaultWhere, 'Set dir', $tree);
        $this->addControl($radios);
        $this->addSubmit('changeDir', 'OK');
        return $this;
    }
}
