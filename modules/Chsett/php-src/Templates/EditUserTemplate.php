<?php

namespace KWCMS\modules\Chsett\Templates;


use kalanis\kw_modules\ATemplate;
use KWCMS\modules\Chsett\Lib\FormUsers;


/**
 * Class EditUserTemplate
 * @package KWCMS\modules\Chsett\Templates
 */
class EditUserTemplate extends ATemplate
{
    protected $moduleName = 'Chsett';
    protected $templateName = 'edit_user';

    protected function fillInputs(): void
    {
        $this->addInput('{RECORD_ACTION}');
        $this->addInput('{FORM_START}');
        $this->addInput('{FORM_ERRORS}');
        $this->addInput('{LABEL_LOGIN}');
        $this->addInput('{INPUT_LOGIN}');
        $this->addInput('{ERROR_LOGIN}');
        $this->addInput('{LABEL_DISPLAY}');
        $this->addInput('{INPUT_DISPLAY}');
        $this->addInput('{ERROR_DISPLAY}');
        $this->addInput('{LABEL_GROUP}');
        $this->addInput('{INPUT_GROUP}');
        $this->addInput('{ERROR_GROUP}');
        $this->addInput('{LABEL_CLASS}');
        $this->addInput('{INPUT_CLASS}');
        $this->addInput('{ERROR_CLASS}');
        $this->addInput('{LABEL_DIR}');
        $this->addInput('{INPUT_DIR}');
        $this->addInput('{ERROR_DIR}');
        $this->addInput('{INPUT_SUBMIT}');
        $this->addInput('{INPUT_RESET}');
        $this->addInput('{FORM_END}');
        $this->addInput('{PASS_TEMPLATE}');
        $this->addInput('{CERT_TEMPLATE}');
    }

    /**
     * @param FormUsers $form
     * @param string $title
     * @return $this
     * @throws \kalanis\kw_forms\Exceptions\RenderException
     */
    public function setData(FormUsers $form, string $title): self
    {
        $this->updateItem('{RECORD_ACTION}', $title);
        $this->updateItem('{FORM_START}', $form->renderStart());
        $this->updateItem('{FORM_ERRORS}', $form->renderErrors());
        $this->updateItem('{LABEL_LOGIN}', $form->getControl('name')->renderLabel());
        $this->updateItem('{INPUT_LOGIN}', $form->getControl('name')->renderInput());
        $this->updateItem('{ERROR_LOGIN}', $form->renderControlErrors('name'));
        $this->updateItem('{LABEL_DISPLAY}', $form->getControl('desc')->renderLabel());
        $this->updateItem('{INPUT_DISPLAY}', $form->getControl('desc')->renderInput());
        $this->updateItem('{ERROR_DISPLAY}', $form->renderControlErrors('desc'));
        $this->updateItem('{LABEL_GROUP}', $form->getControl('group')->renderLabel());
        $this->updateItem('{INPUT_GROUP}', $form->getControl('group')->renderInput());
        $this->updateItem('{ERROR_GROUP}', $form->renderControlErrors('group'));
        $this->updateItem('{LABEL_CLASS}', $form->getControl('class')->renderLabel());
        $this->updateItem('{INPUT_CLASS}', $form->getControl('class')->renderInput());
        $this->updateItem('{ERROR_CLASS}', $form->renderControlErrors('class'));
        $this->updateItem('{LABEL_DIR}', $form->getControl('dir')->renderLabel());
        $this->updateItem('{INPUT_DIR}', $form->getControl('dir')->renderInput());
        $this->updateItem('{ERROR_DIR}', $form->renderControlErrors('dir'));
        $this->updateItem('{INPUT_SUBMIT}', $form->getControl('saveProp')->renderInput());
        $this->updateItem('{INPUT_RESET}', $form->getControl('resetProp')->renderInput());
        $this->updateItem('{FORM_END}', $form->renderEnd());
        return $this;
    }

    public function addPass(string $passTemplate): self
    {
        $this->updateItem('{PASS_TEMPLATE}', $passTemplate);
        return $this;
    }

    public function addCerts(string $certTemplate): self
    {
        $this->updateItem('{CERT_TEMPLATE}', $certTemplate);
        return $this;
    }
}
