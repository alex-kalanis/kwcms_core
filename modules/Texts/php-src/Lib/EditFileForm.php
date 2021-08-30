<?php

namespace KWCMS\modules\Texts\Lib;


use kalanis\kw_forms\Controls;
use kalanis\kw_forms\Form;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_langs\Lang;


/**
 * Class EditFileForm
 * @package KWCMS\modules\Texts\Lib
 * Edit file
 * @property Controls\Textarea content
 * @property Controls\Submit saveFile
 * @property Controls\Reset resetFile
 */
class EditFileForm extends Form
{
    public function composeForm(string $defaultContent, string $fileName, string $targetLink = ''): self
    {
        $this->setAttribute('id', $this->getAlias());
        $this->setMethod(IEntry::SOURCE_POST);
        $this->addTextarea('content', Lang::get('texts.edit_file'), $defaultContent, [
            'cols' => 80, 'rows' => 50,
        ]);
        $this->addHidden('fileName', $fileName);
        $link = $this->addHidden('targetLink', $targetLink);
        $link->setAttribute('id', 'editTargetLink');
        $this->addSubmit('saveFile', Lang::get('texts.save_file'));
        $this->addReset('resetFile', Lang::get('dashboard.button_reset'));
        return $this;
    }
}
