<?php

namespace KWCMS\modules\Short\Lib;


use kalanis\kw_modules\ATemplate;


/**
 * Class EditTemplate
 * @package KWCMS\modules\Short\Lib
 */
class EditTemplate extends ATemplate
{
    protected $moduleName = 'Short';
    protected $templateName = 'edit';

    protected function fillInputs(): void
    {
        $this->addInput('{RECORD_ACTION}');
        $this->addInput('{LABEL_TITLE}');
        $this->addInput('{LABEL_CONTENT}');
        $this->addInput('{INPUT_TITLE}');
        $this->addInput('{INPUT_CONTENT}');
        $this->addInput('{INPUT_SUBMIT}');
        $this->addInput('{INPUT_RESET}');
    }

    /**
     * @param MessageForm $form
     * @param string $action
     * @return EditTemplate
     * @throws \kalanis\kw_forms\Exceptions\RenderException
     */
    public function setData(MessageForm $form, string $action): self
    {
        $this->updateItem('{RECORD_ACTION}', $action);
        $this->updateItem('{LABEL_TITLE}', $form->getControl('title')->renderLabel());
        $this->updateItem('{LABEL_CONTENT}', $form->getControl('content')->renderLabel());
        $this->updateItem('{INPUT_TITLE}', $form->getControl('title')->renderInput());
        $this->updateItem('{INPUT_CONTENT}', $form->getControl('content')->renderInput());
        $this->updateItem('{INPUT_SUBMIT}', $form->getControl('postMessage')->renderInput());
        $this->updateItem('{INPUT_RESET}', $form->getControl('clearMessage')->renderInput());
        return $this;
    }
}
