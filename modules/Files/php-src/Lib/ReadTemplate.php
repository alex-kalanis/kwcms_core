<?php

namespace KWCMS\modules\Files\Lib;


use kalanis\kw_forms\Exceptions\RenderException;
use kalanis\kw_modules\Templates\ATemplate;


/**
 * Class ReadTemplate
 * @package KWCMS\modules\Files\Lib
 */
class ReadTemplate extends ATemplate
{
    protected $moduleName = 'Files';
    protected $templateName = 'file_read';

    protected function fillInputs(): void
    {
        $this->addInput('{FILE_ACTION_TEXT}');
        $this->addInput('{FORM_START}');
        $this->addInput('{LABEL_SOURCE}');
        $this->addInput('{INPUT_SOURCE}');
        $this->addInput('{LABEL_TARGET}');
        $this->addInput('{INPUT_TARGET}');
        $this->addInput('{INPUT_SUBMIT}');
        $this->addInput('{INPUT_RESET}');
        $this->addInput('{FORM_END}');
        $this->addInput('{FILE_MIME}');
        $this->addInput('{FILE_CONTENT}');
    }

    /**
     * @param FileForm $form
     * @param string $action
     * @param string $mime
     * @param string $content
     * @return $this
     * @throws RenderException
     */
    public function setData(FileForm $form, string $action, string $mime, string $content): self
    {
        $this->updateItem('{FILE_ACTION_TEXT}', $action);
        $this->updateItem('{FORM_START}', $form->renderStart());
        $this->updateItem('{LABEL_SOURCE}', $form->getControl('sourceName')->renderLabel());
        $this->updateItem('{INPUT_SOURCE}', $form->getControl('sourceName')->renderInput());
        $this->updateItem('{INPUT_SUBMIT}', $form->getControl('saveFile')->renderInput());
        $this->updateItem('{INPUT_RESET}', $form->getControl('resetFile')->renderInput());
        $this->updateItem('{FORM_END}', $form->renderEnd());
        $this->updateItem('{FILE_MIME}', $mime);
        if ('text/' == substr($mime, 0, 5)) { // ONLY TEXT
            $this->updateItem('{FILE_CONTENT}', $content);
        }
        return $this;
    }
}
