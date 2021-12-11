<?php

namespace KWCMS\modules\Menu\Templates;


use kalanis\kw_modules\ATemplate;
use KWCMS\modules\Menu\Forms\InputPosition;


/**
 * Class PositionItemTemplate
 * @package KWCMS\modules\Menu\Templates
 */
class PositionItemTemplate extends ATemplate
{
    protected $moduleName = 'Menu';
    protected $templateName = 'position_item';

    protected function fillInputs(): void
    {
        $this->addInput('{NUMBER}');
        $this->addInput('{LABEL_CONTROL}');
        $this->addInput('{INPUT_CONTROL}');
    }

    /**
     * @param InputPosition $control
     * @return $this
     * @throws \kalanis\kw_forms\Exceptions\RenderException
     */
    public function setData(InputPosition $control): self
    {
        $this->updateItem('{NUMBER}', strval($control->getOriginalValue()));
        $this->updateItem('{LABEL_CONTROL}', $control->renderLabel());
        $this->updateItem('{INPUT_CONTROL}', $control->renderInput());
        return $this;
    }
}
