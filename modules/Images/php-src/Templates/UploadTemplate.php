<?php

namespace KWCMS\modules\Images\Templates;


use kalanis\kw_forms\Exceptions\RenderException;
use kalanis\kw_langs\Lang;
use kalanis\kw_modules\Templates\ATemplate;
use KWCMS\modules\Images\Forms\FileUploadForm;


/**
 * Class UploadTemplate
 * @package KWCMS\modules\Menu\Lib
 */
class UploadTemplate extends ATemplate
{
    protected $moduleName = 'Images';
    protected $templateName = 'upload';

    protected function fillInputs(): void
    {
        $this->addInput('{UPLOAD_FILE}', Lang::get('images.upload.file'));
        $this->addInput('{UPLOAD_NOTE}', Lang::get('images.upload.note'));
        $this->addInput('{FORM_START}');
        $this->addInput('{FORM_END}');
        $this->addInput('{UPLOAD_LABEL}');
        $this->addInput('{UPLOAD_INPUT}');
        $this->addInput('{DESC_LABEL}');
        $this->addInput('{DESC_INPUT}');
        $this->addInput('{INPUT_SUBMIT}');
        $this->addInput('{INPUT_RESET}');
    }

    /**
     * @param FileUploadForm $form
     * @throws RenderException
     * @return $this
     */
    public function setData(FileUploadForm $form): self
    {
        $this->updateItem('{FORM_START}', $form->renderStart());
        $this->updateItem('{UPLOAD_LABEL}', $form->getControl('uploadedFile')->renderLabel());
        $this->updateItem('{UPLOAD_INPUT}', $form->getControl('uploadedFile')->renderInput());
        $this->updateItem('{DESC_LABEL}', $form->getControl('description')->renderLabel());
        $this->updateItem('{DESC_INPUT}', $form->getControl('description')->renderInput());
        $this->updateItem('{INPUT_SUBMIT}', $form->getControl('saveFile')->renderInput());
        $this->updateItem('{INPUT_RESET}', $form->getControl('resetFile')->renderInput());
        $this->updateItem('{FORM_END}', $form->renderEnd());
        return $this;
    }
}
