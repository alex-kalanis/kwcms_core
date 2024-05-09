<?php

namespace KWCMS\modules\Chsett\Templates;


use KWCMS\modules\Core\Libs\ATemplate;
use KWCMS\modules\Chsett\Lib\FormUsers;


/**
 * Class EditPassTemplate
 * @package KWCMS\modules\Chsett\Templates
 */
class EditPassTemplate extends ATemplate
{
    protected string $moduleName = 'Chsett';
    protected string $templateName = 'edit_user_pass';

    protected function fillInputs(): void
    {
        $this->addInput('{LABEL_PASS}');
        $this->addInput('{INPUT_PASS}');
        $this->addInput('{ERROR_PASS}');
    }

    /**
     * @param FormUsers $form
     * @return $this
     * @throws \kalanis\kw_forms\Exceptions\RenderException
     */
    public function setData(FormUsers $form): self
    {
        $this->updateItem('{LABEL_PASS}', $form->getControl('pass')->renderLabel());
        $this->updateItem('{INPUT_PASS}', $form->getControl('pass')->renderInput());
        $this->updateItem('{ERROR_PASS}', $form->renderControlErrors('pass'));
        return $this;
    }
}
