<?php

namespace KWCMS\modules\Pedigree\Lib;


use kalanis\kw_forms\Exceptions\RenderException;
use kalanis\kw_langs\Lang;
use KWCMS\modules\Core\Libs\ATemplate;
use kalanis\kw_pedigree\GetEntries;


/**
 * Class EditTemplate
 * @package KWCMS\modules\Pedigree\Lib
 */
class EditTemplate extends ATemplate
{
    protected $moduleName = 'Pedigree';
    protected $templateName = 'edit';

    protected function fillInputs(): void
    {
        $this->addInput('{RECORD_ACTION}');
        $this->addInput('{FORM_START}');
        $this->addInput('{LABEL_NAME}');
        $this->addInput('{INPUT_NAME}');
        $this->addInput('{ERROR_NAME}');
        $this->addInput('{LABEL_FAMILY}');
        $this->addInput('{INPUT_FAMILY}');
        $this->addInput('{ERROR_FAMILY}');
        $this->addInput('{LABEL_BIRTHDAY}');
        $this->addInput('{INPUT_BIRTHDAY}');
        $this->addInput('{ERROR_BIRTHDAY}');
        $this->addInput('{LABEL_BASIC_INFO}');
        $this->addInput('{INPUT_BASIC_INFO}');
        $this->addInput('{ERROR_BASIC_INFO}');
        $this->addInput('{LABEL_FATHER}');
        $this->addInput('{INPUT_FATHER}');
        $this->addInput('{ERROR_FATHER}');
        $this->addInput('{LABEL_MOTHER}');
        $this->addInput('{INPUT_MOTHER}');
        $this->addInput('{ERROR_MOTHER}');
        $this->addInput('{LABEL_SEX}');
        $this->addInput('{INPUT_SEX}');
        $this->addInput('{ERROR_SEX}');
        $this->addInput('{LABEL_FULL}');
        $this->addInput('{INPUT_FULL}');
        $this->addInput('{ERROR_FULL}');
        $this->addInput('{LABEL_KEY}');
        $this->addInput('{INPUT_KEY}');
        $this->addInput('{ERROR_KEY}');
        $this->addInput('{INPUT_SUBMIT}');
        $this->addInput('{INPUT_RESET}');
        $this->addInput('{FORM_END}');
        $this->addInput('{BIRTH_DATE_FORM}', Lang::get('pedigree.text.birth_format'));
        $this->addInput('{PART_OF_NAME}', Lang::get('pedigree.text.part_of_name'));
    }

    /**
     * @param MessageForm $form form to fill
     * @param GetEntries $entries keys of each entry
     * @param string $action
     * @throws RenderException
     * @return $this
     */
    public function setData(MessageForm $form, GetEntries $entries, string $action): self
    {
        $this->updateItem('{RECORD_ACTION}', $action);
        $this->updateItem('{FORM_START}', $form->renderStart());
        $this->updateItem('{LABEL_NAME}', $form->getControl($entries->getStorage()->getNameKey())->renderLabel());
        $this->updateItem('{INPUT_NAME}', $form->getControl($entries->getStorage()->getNameKey())->renderInput());
        $this->updateItem('{ERROR_NAME}', $form->renderControlErrors($entries->getStorage()->getNameKey()));
        $this->updateItem('{LABEL_FAMILY}', $form->getControl($entries->getStorage()->getFamilyKey())->renderLabel());
        $this->updateItem('{INPUT_FAMILY}', $form->getControl($entries->getStorage()->getFamilyKey())->renderInput());
        $this->updateItem('{ERROR_FAMILY}', $form->renderControlErrors($entries->getStorage()->getFamilyKey()));
        $this->updateItem('{LABEL_BIRTHDAY}', $form->getControl($entries->getStorage()->getBirthKey())->renderLabel());
        $this->updateItem('{INPUT_BIRTHDAY}', $form->getControl($entries->getStorage()->getBirthKey())->renderInput());
        $this->updateItem('{ERROR_BIRTHDAY}', $form->renderControlErrors($entries->getStorage()->getBirthKey()));
        $this->updateItem('{LABEL_BASIC_INFO}', $form->getControl($entries->getStorage()->getSuccessesKey())->renderLabel());
        $this->updateItem('{INPUT_BASIC_INFO}', $form->getControl($entries->getStorage()->getSuccessesKey())->renderInput());
        $this->updateItem('{ERROR_BASIC_INFO}', $form->renderControlErrors($entries->getStorage()->getSuccessesKey()));
        $this->updateItem('{LABEL_FATHER}', $form->getControl('fatherId')->renderLabel());
        $this->updateItem('{INPUT_FATHER}', $form->getControl('fatherId')->renderInput());
        $this->updateItem('{ERROR_FATHER}', $form->renderControlErrors('fatherId'));
        $this->updateItem('{LABEL_MOTHER}', $form->getControl('motherId')->renderLabel());
        $this->updateItem('{INPUT_MOTHER}', $form->getControl('motherId')->renderInput());
        $this->updateItem('{ERROR_MOTHER}', $form->renderControlErrors('motherId'));
        $this->updateItem('{LABEL_SEX}', $form->getControl($entries->getStorage()->getSexKey())->renderLabel());
        $this->updateItem('{INPUT_SEX}', $form->getControl($entries->getStorage()->getSexKey())->renderInput());
        $this->updateItem('{ERROR_SEX}', $form->renderControlErrors($entries->getStorage()->getSexKey()));
        $this->updateItem('{LABEL_FULL}', $form->getControl($entries->getStorage()->getTextKey())->renderLabel());
        $this->updateItem('{INPUT_FULL}', $form->getControl($entries->getStorage()->getTextKey())->renderInput());
        $this->updateItem('{ERROR_FULL}', $form->renderControlErrors($entries->getStorage()->getTextKey()));
        if ($form->getControl($entries->getStorage()->getShortKey())) {
            $this->updateItem('{LABEL_KEY}', $form->getControl($entries->getStorage()->getShortKey())->renderLabel());
            $this->updateItem('{INPUT_KEY}', $form->getControl($entries->getStorage()->getShortKey())->renderInput());
            $this->updateItem('{ERROR_KEY}', $form->renderControlErrors($entries->getStorage()->getShortKey()));
        }
        $this->updateItem('{INPUT_SUBMIT}', $form->getControl('postRecord')->renderInput());
        $this->updateItem('{INPUT_RESET}', $form->getControl('clearRecord')->renderInput());
        $this->updateItem('{FORM_END}', $form->renderEnd());
        return $this;
    }
}
