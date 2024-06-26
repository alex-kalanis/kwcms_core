<?php

namespace KWCMS\modules\Admin\Templates;


use kalanis\kw_forms\Exceptions\RenderException;
use kalanis\kw_langs\Lang;
use KWCMS\modules\Core\Libs\ATemplate;
use KWCMS\modules\Admin\Forms\LoginForm;


/**
 * Class LoginTemplate
 * @package KWCMS\modules\Admin\Templates
 */
class LoginTemplate extends ATemplate
{
    protected string $moduleName = 'Admin';
    protected string $templateName = 'login';

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
        $this->addInput('{CHANGE_LANG_LINK}', '');
    }

    /**
     * @param LoginForm $form
     * @param string $langLink
     * @throws RenderException
     * @return $this
     */
    public function setData(LoginForm $form, string $langLink = ''): self
    {
        $this->updateItem('{INPUT_NAME}', $form->getControl('user')->renderInput());
        $this->updateItem('{INPUT_PASS}', $form->getControl('pass')->renderInput());
        $this->updateItem('{INPUT_LANG}', $form->getControl('lang') ? $form->getControl('lang')->renderInput() : '');
        $this->updateItem('{INPUT_BUTTON}', $form->getControl('login')->renderInput());
        $this->updateItem('{FORM_ERROR}', $form->renderErrors());
        $this->updateItem('{CHANGE_LANG_LINK}', $langLink);
        return $this;
    }
}
