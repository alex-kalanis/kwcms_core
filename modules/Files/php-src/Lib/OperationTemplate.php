<?php

namespace KWCMS\modules\Files\Lib;


use kalanis\kw_forms\Exceptions\RenderException;
use kalanis\kw_modules\ATemplate;


/**
 * Class OperationTemplate
 * @package KWCMS\modules\Files\Lib
 */
class OperationTemplate extends ATemplate
{
    protected $moduleName = 'Files';
    protected $templateName = 'file_oper';

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
    }

    /**
     * @param FileForm $form
     * @param string $action
     * @return $this
     * @throws RenderException
     */
    public function setData(FileForm $form, string $action): self
    {
        $this->updateItem('{FILE_ACTION_TEXT}', $action);
        $this->updateItem('{FORM_START}', $form->renderStart());
        if ($form->getControl('sourceName[]')) {
            $this->updateItem('{LABEL_SOURCE}', $form->getControl('sourceName[]')->renderLabel());
            $this->updateItem('{INPUT_SOURCE}', $form->getControl('sourceName[]')->renderInput());
        }
        if ($form->getControl('sourceName')) {
            $this->updateItem('{LABEL_SOURCE}', $form->getControl('sourceName')->renderLabel());
            $this->updateItem('{INPUT_SOURCE}', $form->getControl('sourceName')->renderInput());
        }
        if ($form->getControl('targetPath')) {
            $this->updateItem('{LABEL_TARGET}', $form->getControl('targetPath')->renderLabel());
            $this->updateItem('{INPUT_TARGET}', $form->getControl('targetPath')->renderInput());
        }
        $this->updateItem('{INPUT_SUBMIT}', $form->getControl('saveFile')->renderInput());
        $this->updateItem('{INPUT_RESET}', $form->getControl('resetFile')->renderInput());
        $this->updateItem('{FORM_END}', $form->renderEnd());
        return $this;
    }
}
