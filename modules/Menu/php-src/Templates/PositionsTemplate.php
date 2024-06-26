<?php

namespace KWCMS\modules\Menu\Templates;


use kalanis\kw_forms\Exceptions\RenderException;
use kalanis\kw_langs\Lang;
use KWCMS\modules\Core\Libs\ATemplate;
use KWCMS\modules\Menu\Forms\EditPosForm;


/**
 * Class PositionsTemplate
 * @package KWCMS\modules\Menu\Templates
 */
class PositionsTemplate extends ATemplate
{
    protected string $moduleName = 'Menu';
    protected string $templateName = 'positions';

    protected function fillInputs(): void
    {
        $this->addInput('{FILES_POSITIONING}', Lang::get('menu.file_pos'));
        $this->addInput('{FILENAME}', Lang::get('menu.entry_name'));
        $this->addInput('{POSITION}', Lang::get('menu.position'));
        $this->addInput('{FORM_START}');
        $this->addInput('{FORM_END}');
        $this->addInput('{CONTENT}');
        $this->addInput('{INPUT_SUBMIT}');
        $this->addInput('{INPUT_RESET}');
    }

    /**
     * @param EditPosForm $form
     * @param string $content
     * @throws RenderException
     * @return $this
     */
    public function setData(EditPosForm $form, string $content): self
    {
        $this->updateItem('{FORM_START}', $form->renderStart());
        $this->updateItem('{CONTENT}', $content);
        $this->updateItem('{INPUT_SUBMIT}', $form->getControl('saveFile')->renderInput());
        $this->updateItem('{INPUT_RESET}', $form->getControl('resetFile')->renderInput());
        $this->updateItem('{FORM_END}', $form->renderEnd());
        return $this;
    }
}
