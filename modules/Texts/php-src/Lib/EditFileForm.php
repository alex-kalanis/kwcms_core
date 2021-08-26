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
 * @property Controls\Hidden content_base64
 * @property Controls\Submit saveFile
 * @property Controls\Reset resetFile
 */
class EditFileForm extends Form
{
    public function composeForm(string $defaultContent): self
    {
        $this->setMethod(IEntry::SOURCE_POST);
        $this->addTextarea('content', Lang::get('texts.edit_file'), $defaultContent, [
            'cols' => 80, 'rows' => 50,
        ]);
        $this->addHidden('content_base64', base64_encode($defaultContent));
        $this->addSubmit('saveFile', Lang::get('texts.save_file'));
        $this->addReset('resetFile', Lang::get('dashboard.button_reset'));
        return $this;
    }
}
