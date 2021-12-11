<?php

namespace KWCMS\modules\Menu\Templates;


use kalanis\kw_forms\Exceptions\RenderException;
use kalanis\kw_langs\Lang;
use kalanis\kw_modules\ATemplate;
use KWCMS\modules\Menu\Lib\EditPropsForm;


/**
 * Class DashboardTemplate
 * @package KWCMS\modules\Menu\Templates
 */
class DashboardTemplate extends ATemplate
{
    protected $moduleName = 'Menu';
    protected $templateName = 'dashboard';

    protected function fillInputs(): void
    {
        $this->addInput('{CURRENT_DIR_PROPS}', Lang::get('menu.current_dir_props'));
        $this->addInput('{FORM_START}');
        $this->addInput('{LABEL_CURRENT_DIR}');
        $this->addInput('{INPUT_CURRENT_DIR}');
        $this->addInput('{LABEL_MENU_NAME}');
        $this->addInput('{INPUT_MENU_NAME}');
        $this->addInput('{LABEL_MENU_DESC}');
        $this->addInput('{INPUT_MENU_DESC}');
        $this->addInput('{LABEL_MENU_COUNT}');
        $this->addInput('{INPUT_MENU_COUNT}');
        $this->addInput('{INPUT_SUBMIT}');
        $this->addInput('{INPUT_RESET}');
        $this->addInput('{FORM_END}');
    }

    /**
     * @param EditPropsForm $form
     * @return $this
     * @throws RenderException
     */
    public function setData(EditPropsForm $form): self
    {
        $this->updateItem('{FORM_START}', $form->renderStart());
        $this->updateItem('{LABEL_CURRENT_DIR}', $form->getControl('current')->renderLabel());
        $this->updateItem('{INPUT_CURRENT_DIR}', $form->getControl('current')->renderInput());
        $this->updateItem('{LABEL_MENU_NAME}', $form->getControl('menuName')->renderLabel());
        $this->updateItem('{INPUT_MENU_NAME}', $form->getControl('menuName')->renderInput());
        $this->updateItem('{LABEL_MENU_DESC}', $form->getControl('menuDesc')->renderLabel());
        $this->updateItem('{INPUT_MENU_DESC}', $form->getControl('menuDesc')->renderInput());
        $this->updateItem('{LABEL_MENU_COUNT}', $form->getControl('menuCount')->renderLabel());
        $this->updateItem('{INPUT_MENU_COUNT}', $form->getControl('menuCount')->renderInput());
        $this->updateItem('{INPUT_SUBMIT}', $form->getControl('saveFile')->renderInput());
        $this->updateItem('{INPUT_RESET}', $form->getControl('resetFile')->renderInput());
        $this->updateItem('{FORM_END}', $form->renderEnd());
        return $this;
    }
}
