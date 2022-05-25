<?php

namespace KWCMS\modules\Admin\Templates;


use kalanis\kw_langs\Lang;
use kalanis\kw_modules\Templates\ATemplate;
use KWCMS\modules\Admin\Forms\LoginForm;


/**
 * Class LoginTemplate
 * @package KWCMS\modules\Admin\Templates
 */
class LoginTemplate extends ATemplate
{
    protected $moduleName = 'Admin';
    protected $templateName = 'login';

    protected function fillInputs(): void
    {
        $this->addInput('{INPUT_NAME}');
        $this->addInput('{INPUT_PASS}');
        $this->addInput('{INPUT_CAPTCHA}', '');
        $this->addInput('{INPUT_BUTTON}');
        $this->addInput('{INPUT_LANG}', '');
        $this->addInput('{FORM_ERROR}', '');
        $this->addInput('{LOGIN_TEXT}', Lang::get('login.text'));
        $this->addInput('{TITLE_NAME}', Lang::get('login.name'));
        $this->addInput('{TITLE_PASS}', Lang::get('login.pass'));
        $this->addInput('{TITLE_LANG}', Lang::get('system.use_lang'));
        $this->addInput('{LOGIN_STAT}', '');
    }

    public function setData(LoginForm $form): self
    {
        $this->updateItem('{INPUT_NAME}', $form->getControl('user')->renderInput());
        $this->updateItem('{INPUT_PASS}', $form->getControl('pass')->renderInput());
//        $this->updateItem('{INPUT_LANG}', $form->getControl('lang')->renderInput());
        $this->updateItem('{INPUT_BUTTON}', $form->getControl('login')->renderInput());
        $this->updateItem('{FORM_ERROR}', $form->renderErrors());
        return $this;
    }
}
