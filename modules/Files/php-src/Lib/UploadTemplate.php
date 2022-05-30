<?php

namespace KWCMS\modules\Files\Lib;


use kalanis\kw_forms\Exceptions\RenderException;
use kalanis\kw_langs\Lang;
use kalanis\kw_modules\Templates\ATemplate;


/**
 * Class UploadTemplate
 * @package KWCMS\modules\Files\Lib
 */
class UploadTemplate extends ATemplate
{
    protected $moduleName = 'Files';
    protected $templateName = 'file_upload';

    protected function fillInputs(): void
    {
        $this->addInput('{FILE_UPLOAD_TEXT}', Lang::get('files.file.upload'));
        $this->addInput('{LABEL_FILE}', Lang::get('files.file.upload'));
        $this->addInput('{FORM_START}');
        $this->addInput('{INPUT_FILE}');
        $this->addInput('{INPUT_SUBMIT}');
        $this->addInput('{INPUT_RESET}');
        $this->addInput('{FORM_END}');
    }

    /**
     * @param FileForm $form
     * @return $this
     * @throws RenderException
     */
    public function setData(FileForm $form): self
    {
        $this->updateItem('{FORM_START}', $form->renderStart());
        $this->updateItem('{INPUT_FILE}', $form->getControl('uploadedFile')->renderInput());
        $this->updateItem('{INPUT_SUBMIT}', $form->getControl('saveFile')->renderInput());
        $this->updateItem('{INPUT_RESET}', $form->getControl('resetFile')->renderInput());
        $this->updateItem('{FORM_END}', $form->renderEnd());
        return $this;
    }
}
