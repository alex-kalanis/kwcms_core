<?php

namespace KWCMS\modules\Texts\Lib;


use kalanis\kw_forms\Controls;
use kalanis\kw_forms\Form;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_langs\Lang;


/**
 * Class NewFileForm
 * @package KWCMS\modules\Texts\Lib
 * Create new file / open undeclared file
 * @property Controls\Text fileName
 * @property Controls\Submit openFile
 * @property Controls\Reset resetFile
 */
class NewFileForm extends Form
{
    public function composeForm(string $editLink): self
    {
        $this->setMethod(IEntry::SOURCE_GET);
        $this->setAttribute('action', $editLink);
        $this->addText('fileName', Lang::get('texts.file_name'));
        $this->addSubmit('openFile', Lang::get('dashboard.button_ok'));
        $this->addReset('resetFile', Lang::get('dashboard.button_reset'));
        return $this;
    }
}
