<?php

namespace KWCMS\modules\Images\Forms;


use kalanis\kw_forms\Controls;
use kalanis\kw_forms\Form;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_langs\Lang;
use kalanis\kw_rules\Interfaces\IRules;


/**
 * Class FileUploadForm
 * @package KWCMS\modules\Images\Forms
 * Upload new file - image
 * @property Controls\File $uploadedFile
 * @property Controls\Text $description
 * @property Controls\Submit $saveDir
 * @property Controls\Reset $resetDir
 */
class FileUploadForm extends Form
{
    public function composeForm(): self
    {
        $this->setMethod(IEntry::SOURCE_POST);
        $input = $this->addFile('uploadedFile', Lang::get('images.file.select'));
        $input->addRule(IRules::FILE_RECEIVED, Lang::get('images.must_be_sent'));
        $input->addRule(IRules::IS_IMAGE, Lang::get('images.must_be_sent'));
        $this->addText('description', Lang::get('images.description'));
        $this->addCheckbox('rotate', Lang::get('images.rotate'));
        $this->addSubmit('saveFile', Lang::get('dashboard.button_ok'));
        $this->addReset('resetFile', Lang::get('dashboard.button_reset'));
        return $this;
    }
}
