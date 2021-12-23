<?php

namespace KWCMS\modules\Personal\Lib;


use kalanis\kw_forms\Controls;
use kalanis\kw_forms\Form;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_langs\Lang;
use kalanis\kw_rules\Interfaces\IRules;


/**
 * Class FormPass
 * @package KWCMS\modules\Personal\Lib
 * Edit description of dir/file
 * @property Controls\Password currentPass
 * @property Controls\Password newPass
 * @property Controls\Password newPass2
 * @property Controls\Submit saveDesc
 * @property Controls\Reset resetDesc
 */
class FormPass extends Form
{
    public function composeForm(): self
    {
        $this->setMethod(IEntry::SOURCE_POST);
        $this->addPassword('currentPass', Lang::get('personal.current_pass'))
            ->addRule(IRules::IS_NOT_EMPTY, Lang::get('personal.old_pass_empty'));
        $this->addPassword('newPass', Lang::get('personal.new_pass'))
            ->addRule(IRules::IS_NOT_EMPTY, Lang::get('personal.new_pass_empty'));
        $np = $this->addPassword('newPass2', Lang::get('personal.new_pass_again'));
        $np->addRule(IRules::IS_NOT_EMPTY, Lang::get('personal.retry_pass_empty'));
        $np->addRule(IRules::SATISFIES_CALLBACK, Lang::get('personal.different_checks'), [$this, 'ruleCompare']);
        $this->addSubmit('saveProp', Lang::get('dashboard.button_set'));
        $this->addReset('resetProp', Lang::get('dashboard.button_reset'));
        return $this;
    }

    public function ruleCompare($value): bool
    {
        return $this->getControl('newPass')->getValue() == $value;
    }
}
