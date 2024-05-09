<?php

namespace KWCMS\modules\Personal\Templates;


use kalanis\kw_forms\Exceptions\RenderException;
use kalanis\kw_langs\Lang;
use KWCMS\modules\Core\Libs\ATemplate;
use KWCMS\modules\Personal\Lib\FormProps;


/**
 * Class EditTemplate
 * @package KWCMS\modules\Personal\Templates
 */
class EditTemplate extends ATemplate
{
    protected string $moduleName = 'Personal';
    protected string $templateName = 'edit';

    protected function fillInputs(): void
    {
        $this->addInput('{RECORD_ACTION}', Lang::get('personal.account_properties'));
        $this->addInput('{FORM_START}');
        $this->addInput('{FORM_ERRORS}');
        $this->addInput('{LABEL_LOGIN}');
        $this->addInput('{INPUT_LOGIN}');
        $this->addInput('{ERROR_LOGIN}');
        $this->addInput('{LABEL_DISPLAY}');
        $this->addInput('{INPUT_DISPLAY}');
        $this->addInput('{ERROR_DISPLAY}');
        $this->addInput('{INPUT_SUBMIT}');
        $this->addInput('{INPUT_RESET}');
        $this->addInput('{FORM_END}');
        $this->addInput('{CERT_TEMPLATE}');
    }

    /**
     * @param FormProps $form
     * @throws RenderException
     * @return $this
     */
    public function setData(FormProps $form): self
    {
        $this->updateItem('{FORM_START}', $form->renderStart());
        $this->updateItem('{FORM_ERRORS}', $form->renderErrors());
        $this->updateItem('{LABEL_LOGIN}', $form->getControl('loginName')->renderLabel());
        $this->updateItem('{INPUT_LOGIN}', $form->getControl('loginName')->renderInput());
        $this->updateItem('{ERROR_LOGIN}', $form->renderControlErrors('loginName'));
        $this->updateItem('{LABEL_DISPLAY}', $form->getControl('displayName')->renderLabel());
        $this->updateItem('{INPUT_DISPLAY}', $form->getControl('displayName')->renderInput());
        $this->updateItem('{ERROR_DISPLAY}', $form->renderControlErrors('displayName'));
        $this->updateItem('{INPUT_SUBMIT}', $form->getControl('saveProp')->renderInput());
        $this->updateItem('{INPUT_RESET}', $form->getControl('resetProp')->renderInput());
        $this->updateItem('{FORM_END}', $form->renderEnd());
        return $this;
    }

    public function addCerts(string $certTemplate): self
    {
        $this->updateItem('{CERT_TEMPLATE}', $certTemplate);
        return $this;
    }
}
