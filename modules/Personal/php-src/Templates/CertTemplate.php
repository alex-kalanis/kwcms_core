<?php

namespace KWCMS\modules\Personal\Templates;


use kalanis\kw_forms\Exceptions\RenderException;
use kalanis\kw_modules\Templates\ATemplate;
use KWCMS\modules\Personal\Lib\FormProps;


/**
 * Class CertTemplate
 * @package KWCMS\modules\Personal\Templates
 */
class CertTemplate extends ATemplate
{
    protected $moduleName = 'Personal';
    protected $templateName = 'cert';

    protected function fillInputs(): void
    {
        $this->addInput('{LABEL_KEY}');
        $this->addInput('{INPUT_KEY}');
        $this->addInput('{LABEL_SALT}');
        $this->addInput('{INPUT_SALT}');
    }

    /**
     * @param FormProps $form
     * @throws RenderException
     * @return $this
     */
    public function setData(FormProps $form): self
    {
        $this->updateItem('{LABEL_KEY}', $form->getControl('pubKey')->renderLabel());
        $this->updateItem('{INPUT_KEY}', $form->getControl('pubKey')->renderInput());
        $this->updateItem('{LABEL_SALT}', $form->getControl('pubSalt')->renderLabel());
        $this->updateItem('{INPUT_SALT}', $form->getControl('pubSalt')->renderInput());
        return $this;
    }
}
