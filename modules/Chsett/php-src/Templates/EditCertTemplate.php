<?php

namespace KWCMS\modules\Chsett\Templates;


use kalanis\kw_modules\Templates\ATemplate;
use KWCMS\modules\Chsett\Lib\FormUsers;


/**
 * Class EditCertTemplate
 * @package KWCMS\modules\Chsett\Templates
 */
class EditCertTemplate extends ATemplate
{
    protected $moduleName = 'Chsett';
    protected $templateName = 'edit_user_cert';

    protected function fillInputs(): void
    {
        $this->addInput('{LABEL_KEY}');
        $this->addInput('{INPUT_KEY}');
        $this->addInput('{LABEL_SALT}');
        $this->addInput('{INPUT_SALT}');
    }

    /**
     * @param FormUsers $form
     * @return $this
     * @throws \kalanis\kw_forms\Exceptions\RenderException
     */
    public function setData(FormUsers $form): self
    {
        $this->updateItem('{LABEL_KEY}', $form->getControl('pubKey')->renderLabel());
        $this->updateItem('{INPUT_KEY}', $form->getControl('pubKey')->renderInput());
        $this->updateItem('{LABEL_SALT}', $form->getControl('pubSalt')->renderLabel());
        $this->updateItem('{INPUT_SALT}', $form->getControl('pubSalt')->renderInput());
        return $this;
    }
}
