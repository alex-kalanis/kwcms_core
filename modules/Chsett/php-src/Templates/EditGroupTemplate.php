<?php

namespace KWCMS\modules\Chsett\Templates;


use KWCMS\modules\Core\Libs\ATemplate;
use KWCMS\modules\Chsett\Lib\FormGroups;


/**
 * Class EditGroupTemplate
 * @package KWCMS\modules\Chsett\Templates
 */
class EditGroupTemplate extends ATemplate
{
    protected string $moduleName = 'Chsett';
    protected string $templateName = 'edit_group';

    protected function fillInputs(): void
    {
        $this->addInput('{RECORD_ACTION}');
        $this->addInput('{FORM_START}');
        $this->addInput('{FORM_ERRORS}');
        $this->addInput('{LABEL_NAME}');
        $this->addInput('{INPUT_NAME}');
        $this->addInput('{ERROR_NAME}');
        $this->addInput('{LABEL_DESC}');
        $this->addInput('{INPUT_DESC}');
        $this->addInput('{ERROR_DESC}');
        $this->addInput('{INPUT_SUBMIT}');
        $this->addInput('{INPUT_RESET}');
        $this->addInput('{FORM_END}');
    }

    /**
     * @param FormGroups $form
     * @param string $title
     * @return $this
     * @throws \kalanis\kw_forms\Exceptions\RenderException
     */
    public function setData(FormGroups $form, string $title): self
    {
        $this->updateItem('{RECORD_ACTION}', $title);
        $this->updateItem('{FORM_START}', $form->renderStart());
        $this->updateItem('{FORM_ERRORS}', $form->renderErrors());
        $this->updateItem('{LABEL_NAME}', $form->getControl('name')->renderLabel());
        $this->updateItem('{INPUT_NAME}', $form->getControl('name')->renderInput());
        $this->updateItem('{ERROR_NAME}', $form->renderControlErrors('name'));
        $this->updateItem('{LABEL_DESC}', $form->getControl('desc')->renderLabel());
        $this->updateItem('{INPUT_DESC}', $form->getControl('desc')->renderInput());
        $this->updateItem('{ERROR_DESC}', $form->renderControlErrors('desc'));
        $this->updateItem('{INPUT_SUBMIT}', $form->getControl('saveProp')->renderInput());
        $this->updateItem('{INPUT_RESET}', $form->getControl('resetProp')->renderInput());
        $this->updateItem('{FORM_END}', $form->renderEnd());
        return $this;
    }
}
