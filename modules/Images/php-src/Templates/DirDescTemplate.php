<?php

namespace KWCMS\modules\Images\Templates;


use kalanis\kw_langs\Lang;
use kalanis\kw_modules\ATemplate;
use KWCMS\modules\Images\Forms\DirDescForm;


/**
 * Class DirDescTemplate
 * @package KWCMS\modules\Menu\Lib
 */
class DirDescTemplate extends ATemplate
{
    protected $moduleName = 'Images';
    protected $templateName = 'dir_props';

    protected function fillInputs(): void
    {
        $this->addInput('{DIR_PROPS}', Lang::get('Dir props'));
        $this->addInput('{FORM_START}');
        $this->addInput('{FORM_END}');
        $this->addInput('{DESC_LABEL}');
        $this->addInput('{DESC_INPUT}');
        $this->addInput('{INPUT_SUBMIT}');
        $this->addInput('{INPUT_RESET}');
    }

    /**
     * @param DirDescForm $form
     * @return $this
     * @throws \kalanis\kw_forms\Exceptions\RenderException
     */
    public function setData(DirDescForm $form): self
    {
        $this->updateItem('{FORM_START}', $form->renderStart());
        $this->updateItem('{DESC_LABEL}', $form->getControl('name')->renderLabel());
        $this->updateItem('{DESC_INPUT}', $form->getControl('name')->renderInput());
        $this->updateItem('{INPUT_SUBMIT}', $form->getControl('saveDir')->renderInput());
        $this->updateItem('{INPUT_RESET}', $form->getControl('resetDir')->renderInput());
        $this->updateItem('{FORM_END}', $form->renderEnd());
        return $this;
    }
}