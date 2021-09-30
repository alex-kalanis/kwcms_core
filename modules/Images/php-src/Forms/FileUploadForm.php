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
 * @property Controls\File uploadedFile
 * @property Controls\Text description
 * @property Controls\Submit saveDir
 * @property Controls\Reset resetDir
 */
class FileUploadForm extends Form
{
    public function composeForm(): self
    {
        $this->setMethod(IEntry::SOURCE_POST);
        $input = $this->addFile('uploadedFile', Lang::get('files.file.select'));
        $input->addRule(IRules::FILE_RECEIVED, Lang::get('files.must_be_sent'));
        $input->addRule(IRules::IS_IMAGE, Lang::get('files.must_be_sent'));
        $this->addText('description', Lang::get('menu.current_dir'));
        $this->addSubmit('saveFile', Lang::get('dashboard.button_ok'));
        $this->addReset('resetFile', Lang::get('dashboard.button_reset'));
        return $this;
    }
}
