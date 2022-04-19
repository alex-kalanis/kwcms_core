<?php

namespace KWCMS\modules\Texts\Lib;


use kalanis\kw_forms\Controls;
use kalanis\kw_forms\Form;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_langs\Lang;
use kalanis\kw_tree\FileNode;
use kalanis\kw_tree_controls\Controls\FileRadio;


/**
 * Class OpenFileForm
 * @package KWCMS\modules\Texts\Lib
 * Open file
 * @property FileRadio fileName
 * @property Controls\Submit openFile
 * @property Controls\Reset resetFile
 */
class OpenFileForm extends Form
{
    public function composeForm(string $defaultWhere, ?FileNode $tree, string $editLink): self
    {
        $this->setMethod(IEntry::SOURCE_GET);
        $this->setAttribute('action', $editLink);

        $radios = new FileRadio();
        $radios->set('fileName', $defaultWhere, Lang::get('texts.set_file'), $tree);
        $this->addControlDefaultKey($radios);

        $this->addSubmit('openFile', Lang::get('dashboard.button_ok'));
        $this->addReset('resetFile', Lang::get('dashboard.button_reset'));
        return $this;
    }
}
