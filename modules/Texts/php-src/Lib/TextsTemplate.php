<?php

namespace KWCMS\modules\Texts\Lib;


use kalanis\kw_forms\Exceptions\RenderException;
use kalanis\kw_langs\Lang;
use kalanis\kw_modules\Templates\ATemplate;


/**
 * Class TextsTemplate
 * @package KWCMS\modules\Texts\Lib
 */
class TextsTemplate extends ATemplate
{
    protected $moduleName = 'Texts';
    protected $templateName = 'texts';

    protected function fillInputs(): void
    {
        $this->addInput('{OPEN_TEXT}', Lang::get('texts.open_file'));
        $this->addInput('{OPEN_FORM_START}');
        $this->addInput('{INPUT_FILE_TREE}');
        $this->addInput('{OPEN_INPUT_SUBMIT}');
        $this->addInput('{OPEN_INPUT_RESET}');
        $this->addInput('{OPEN_FORM_END}');

        $this->addInput('{NEW_TEXT}', Lang::get('texts.new_file'));
        $this->addInput('{NEW_FORM_START}');
        $this->addInput('{NEW_LABEL_FILENAME}');
        $this->addInput('{NEW_INPUT_FILENAME}');
        $this->addInput('{NEW_INPUT_SUBMIT}');
        $this->addInput('{NEW_INPUT_RESET}');
        $this->addInput('{NEW_FORM_END}');
    }

    /**
     * @param NewFileForm $newForm
     * @param OpenFileForm $openForm
     * @throws RenderException
     * @return $this
     */
    public function setData(NewFileForm $newForm, OpenFileForm $openForm): self
    {
        $this->updateItem('{OPEN_FORM_START}', $openForm->renderStart());
        $this->updateItem('{INPUT_FILE_TREE}', $openForm->getControl('fileName')->renderInput());
        $this->updateItem('{OPEN_INPUT_SUBMIT}', $openForm->getControl('openFile')->renderInput());
        $this->updateItem('{OPEN_INPUT_RESET}', $openForm->getControl('resetFile')->renderInput());
        $this->updateItem('{OPEN_FORM_END}', $openForm->renderEnd());

        $this->updateItem('{NEW_FORM_START}', $newForm->renderStart());
        $this->updateItem('{NEW_LABEL_FILENAME}', $newForm->getControl('fileName')->renderLabel());
        $this->updateItem('{NEW_INPUT_FILENAME}', $newForm->getControl('fileName')->renderInput());
        $this->updateItem('{NEW_INPUT_SUBMIT}', $newForm->getControl('openFile')->renderInput());
        $this->updateItem('{NEW_INPUT_RESET}', $newForm->getControl('resetFile')->renderInput());
        $this->updateItem('{NEW_FORM_END}', $newForm->renderEnd());
        return $this;
    }
}
