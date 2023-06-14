<?php

namespace KWCMS\modules\Images\Templates;


use kalanis\kw_forms\Exceptions\RenderException;
use kalanis\kw_langs\Lang;
use kalanis\kw_modules\Templates\ATemplate;
use KWCMS\modules\Images\Forms\DirExtraForm;


/**
 * Class DirExtraTemplate
 * @package KWCMS\modules\Menu\Lib
 */
class DirExtraTemplate extends ATemplate
{
    protected $moduleName = 'Images';
    protected $templateName = 'dir_extra';

    protected function fillInputs(): void
    {
        $this->addInput('{DIR_PROPS}', Lang::get('images.dir.allow_extra_data'));
        $this->addInput('{FORM_START}');
        $this->addInput('{FORM_END}');
        $this->addInput('{INPUT_SUBMIT}');
    }

    /**
     * @param DirExtraForm $form
     * @throws RenderException
     * @return $this
     */
    public function setData(DirExtraForm $form): self
    {
        $this->updateItem('{FORM_START}', $form->renderStart());
        $this->updateItem('{INPUT_SUBMIT}', $form->getControl('saveDir')->renderInput());
        $this->updateItem('{FORM_END}', $form->renderEnd());
        return $this;
    }
}
