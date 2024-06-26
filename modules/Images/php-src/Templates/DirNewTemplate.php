<?php

namespace KWCMS\modules\Images\Templates;


use kalanis\kw_forms\Exceptions\RenderException;
use kalanis\kw_langs\Lang;
use KWCMS\modules\Core\Libs\ATemplate;
use KWCMS\modules\Images\Forms\DirNewForm;


/**
 * Class DirNewTemplate
 * @package KWCMS\modules\Menu\Lib
 */
class DirNewTemplate extends ATemplate
{
    protected string $moduleName = 'Images';
    protected string $templateName = 'dir_new';

    protected function fillInputs(): void
    {
        $this->addInput('{DIR_NEW}', Lang::get('images.dir.new'));
        $this->addInput('{FORM_START}');
        $this->addInput('{FORM_END}');
        $this->addInput('{DIR_LABEL}');
        $this->addInput('{DIR_INPUT}');
        $this->addInput('{TARGET_LABEL}');
        $this->addInput('{TARGET_INPUT}');
        $this->addInput('{INTO_LABEL}');
        $this->addInput('{INTO_INPUT}');
        $this->addInput('{INPUT_SUBMIT}');
        $this->addInput('{INPUT_RESET}');
    }

    /**
     * @param DirNewForm $form
     * @throws RenderException
     * @return $this
     */
    public function setData(DirNewForm $form): self
    {
        $this->updateItem('{FORM_START}', $form->renderStart());
        $this->updateItem('{DIR_LABEL}', $form->getControl('name')->renderLabel());
        $this->updateItem('{DIR_INPUT}', $form->getControl('name')->renderInput());
        $this->updateItem('{TARGET_LABEL}', $form->getControl('where')->renderLabel());
        $this->updateItem('{TARGET_INPUT}', $form->getControl('where')->renderInput());
        $this->updateItem('{INTO_LABEL}', $form->getControl('into')->renderLabel());
        $this->updateItem('{INTO_INPUT}', $form->getControl('into')->renderInput());
        $this->updateItem('{INPUT_SUBMIT}', $form->getControl('saveDir')->renderInput());
        $this->updateItem('{INPUT_RESET}', $form->getControl('resetDir')->renderInput());
        $this->updateItem('{FORM_END}', $form->renderEnd());
        return $this;
    }

    /**
     * @param DirNewForm $form
     * @throws RenderException
     * @return DirNewTemplate
     */
    public function useTarget(DirNewForm $form): self
    {
        $this->updateItem('{TARGET_LABEL}', $form->getControl('where')->renderLabel());
        $this->updateItem('{TARGET_INPUT}', $form->getControl('where')->renderInput());
        return $this;
    }
}
