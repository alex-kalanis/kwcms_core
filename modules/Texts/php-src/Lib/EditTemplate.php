<?php

namespace KWCMS\modules\Texts\Lib;


use kalanis\kw_forms\Exceptions\RenderException;
use kalanis\kw_langs\Lang;
use kalanis\kw_modules\Templates\ATemplate;


/**
 * Class EditTemplate
 * @package KWCMS\modules\Texts\Lib
 */
class EditTemplate extends ATemplate
{
    protected $moduleName = 'Texts';
    protected $templateName = 'edit';

    protected function fillInputs(): void
    {
        $this->addInput('{NEW_TEXT}', Lang::get('texts.edit_file'));
        $this->addInput('{FORM_START}');
        $this->addInput('{INPUT_CONTENT}');
        $this->addInput('{INPUT_SUBMIT}');
        $this->addInput('{INPUT_RESET}');
        $this->addInput('{FORM_END}');
    }

    /**
     * @param EditFileForm $form
     * @throws RenderException
     * @return $this
     */
    public function setData(EditFileForm $form): self
    {
        $this->updateItem('{FORM_START}', $form->renderStart());
        $this->updateItem('{INPUT_CONTENT}', $form->getControl('content')->renderInput());
        $this->updateItem('{INPUT_SUBMIT}', $form->getControl('saveFile')->renderInput());
        $this->updateItem('{INPUT_RESET}', $form->getControl('resetFile')->renderInput());
        $this->updateItem('{FORM_END}', $form->renderEnd());
        return $this;
    }
}
