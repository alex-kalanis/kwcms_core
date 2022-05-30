<?php

namespace KWCMS\modules\Personal\Templates;


use kalanis\kw_langs\Lang;
use kalanis\kw_modules\Templates\ATemplate;
use KWCMS\modules\Personal\Lib\FormPass;


/**
 * Class PassTemplate
 * @package KWCMS\modules\Personal\Templates
 */
class PassTemplate extends ATemplate
{
    protected $moduleName = 'Personal';
    protected $templateName = 'pass';

    protected function fillInputs(): void
    {
        $this->addInput('{RECORD_ACTION}', Lang::get('personal.change_password'));
        $this->addInput('{FORM_START}');
        $this->addInput('{FORM_ERRORS}');
        $this->addInput('{LABEL_CURRENT}');
        $this->addInput('{INPUT_CURRENT}');
        $this->addInput('{ERROR_CURRENT}');
        $this->addInput('{LABEL_NEW}');
        $this->addInput('{INPUT_NEW}');
        $this->addInput('{ERROR_NEW}');
        $this->addInput('{LABEL_NEW_AGAIN}');
        $this->addInput('{INPUT_NEW_AGAIN}');
        $this->addInput('{ERROR_NEW_AGAIN}');
        $this->addInput('{INPUT_SUBMIT}');
        $this->addInput('{INPUT_RESET}');
        $this->addInput('{FORM_END}');
    }

    /**
     * @param FormPass $form
     * @return $this
     * @throws \kalanis\kw_forms\Exceptions\RenderException
     */
    public function setData(FormPass $form): self
    {
        $this->updateItem('{FORM_START}', $form->renderStart());
        $this->updateItem('{FORM_ERRORS}', $form->renderErrors());
        $this->updateItem('{LABEL_CURRENT}', $form->getControl('currentPass')->renderLabel());
        $this->updateItem('{INPUT_CURRENT}', $form->getControl('currentPass')->renderInput());
        $this->updateItem('{ERROR_CURRENT}', $form->renderControlErrors('currentPass'));
        $this->updateItem('{LABEL_NEW}', $form->getControl('newPass')->renderLabel());
        $this->updateItem('{INPUT_NEW}', $form->getControl('newPass')->renderInput());
        $this->updateItem('{ERROR_NEW}', $form->renderControlErrors('newPass'));
        $this->updateItem('{LABEL_NEW_AGAIN}', $form->getControl('newPass2')->renderLabel());
        $this->updateItem('{INPUT_NEW_AGAIN}', $form->getControl('newPass2')->renderInput());
        $this->updateItem('{ERROR_NEW_AGAIN}', $form->renderControlErrors('newPass2'));
        $this->updateItem('{INPUT_SUBMIT}', $form->getControl('saveProp')->renderInput());
        $this->updateItem('{INPUT_RESET}', $form->getControl('resetProp')->renderInput());
        $this->updateItem('{FORM_END}', $form->renderEnd());
        return $this;
    }
}
