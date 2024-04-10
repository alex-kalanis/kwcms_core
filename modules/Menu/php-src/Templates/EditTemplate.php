<?php

namespace KWCMS\modules\Menu\Templates;


use kalanis\kw_forms\Exceptions\RenderException;
use KWCMS\modules\Core\Libs\ATemplate;
use KWCMS\modules\Menu\Forms\EditNamesForm;


/**
 * Class EditTemplate
 * @package KWCMS\modules\Menu\Templates
 */
class EditTemplate extends ATemplate
{
    protected string $moduleName = 'Menu';
    protected string $templateName = 'edit';

    protected function fillInputs(): void
    {
        $this->addInput('{RECORD_ACTION}');
        $this->addInput('{FORM_START}');
        $this->addInput('{FORM_END}');
        $this->addInput('{LABEL_FILE}');
        $this->addInput('{INPUT_FILE}');
        $this->addInput('{LABEL_NAME}');
        $this->addInput('{INPUT_NAME}');
        $this->addInput('{LABEL_DESC}');
        $this->addInput('{INPUT_DESC}');
        $this->addInput('{LABEL_GO_SUB}');
        $this->addInput('{INPUT_GO_SUB}');
        $this->addInput('{INPUT_SUBMIT}');
        $this->addInput('{INPUT_RESET}');
    }

    /**
     * @param EditNamesForm $form
     * @param string $action
     * @throws RenderException
     * @return $this
     */
    public function setData(EditNamesForm $form, string $action): self
    {
        $this->updateItem('{RECORD_ACTION}', $action);
        $this->updateItem('{FORM_START}', $form->renderStart());
        $this->updateItem('{LABEL_FILE}', $form->getControl('current')->renderLabel());
        $this->updateItem('{INPUT_FILE}', $form->getControl('current')->renderInput());
        $this->updateItem('{LABEL_NAME}', $form->getControl('menuName')->renderLabel());
        $this->updateItem('{INPUT_NAME}', $form->getControl('menuName')->renderInput());
        $this->updateItem('{LABEL_DESC}', $form->getControl('menuDesc')->renderLabel());
        $this->updateItem('{INPUT_DESC}', $form->getControl('menuDesc')->renderInput());
        $this->updateItem('{LABEL_GO_SUB}', $form->getControl('menuGoSub')->renderLabel());
        $this->updateItem('{INPUT_GO_SUB}', $form->getControl('menuGoSub')->renderInput());
        $this->updateItem('{INPUT_SUBMIT}', $form->getControl('saveFile')->renderInput());
        $this->updateItem('{INPUT_RESET}', $form->getControl('resetFile')->renderInput());
        $this->updateItem('{FORM_END}', $form->renderEnd());
        return $this;
    }
}
