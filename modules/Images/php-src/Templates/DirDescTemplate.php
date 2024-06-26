<?php

namespace KWCMS\modules\Images\Templates;


use kalanis\kw_forms\Exceptions\RenderException;
use kalanis\kw_langs\Lang;
use KWCMS\modules\Core\Libs\ATemplate;
use KWCMS\modules\Images\Forms\DescForm;


/**
 * Class DirDescTemplate
 * @package KWCMS\modules\Menu\Lib
 */
class DirDescTemplate extends ATemplate
{
    protected string $moduleName = 'Images';
    protected string $templateName = 'dir_props';

    protected function fillInputs(): void
    {
        $this->addInput('{DIR_PROPS}', Lang::get('images.dir.props'));
        $this->addInput('{FORM_START}');
        $this->addInput('{FORM_END}');
        $this->addInput('{DESC_LABEL}');
        $this->addInput('{DESC_INPUT}');
        $this->addInput('{INPUT_SUBMIT}');
        $this->addInput('{INPUT_RESET}');
    }

    /**
     * @param DescForm $form
     * @throws RenderException
     * @return $this
     */
    public function setData(DescForm $form): self
    {
        $this->updateItem('{FORM_START}', $form->renderStart());
        $this->updateItem('{DESC_LABEL}', $form->getControl('description')->renderLabel());
        $this->updateItem('{DESC_INPUT}', $form->getControl('description')->renderInput());
        $this->updateItem('{INPUT_SUBMIT}', $form->getControl('saveDesc')->renderInput());
        $this->updateItem('{INPUT_RESET}', $form->getControl('resetDesc')->renderInput());
        $this->updateItem('{FORM_END}', $form->renderEnd());
        return $this;
    }
}
