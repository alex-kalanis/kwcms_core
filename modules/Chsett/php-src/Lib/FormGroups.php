<?php

namespace KWCMS\modules\Chsett\Lib;


use kalanis\kw_auth\Interfaces\IGroup;
use kalanis\kw_forms\Controls;
use kalanis\kw_forms\Form;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_langs\Lang;
use kalanis\kw_rules\Interfaces\IRules;


/**
 * Class FormGroups
 * @package KWCMS\modules\Chsett\Lib
 * Edit group props
 * @property Controls\Text name
 * @property Controls\Text desc
 * @property Controls\Submit saveProp
 * @property Controls\Reset resetProp
 */
class FormGroups extends Form
{
    use TStatuses;

    /**
     * @param IGroup $group
     * @return $this
     */
    public function composeForm(IGroup $group): self
    {
        $this->setMethod(IEntry::SOURCE_POST);
        $this->addText('name', Lang::get('chsett.group_name'), $group->getGroupName())
            ->addRule(IRules::IS_NOT_EMPTY, Lang::get('chsett.group_name_empty'));
        $this->addText('desc', Lang::get('chsett.group_desc'), $group->getGroupDesc())
            ->addRule(IRules::IS_NOT_EMPTY, Lang::get('chsett.group_desc_empty'));
        $origStat = is_null($group->getGroupStatus()) ? '' : $group->getGroupStatus();
        $this->addSelect('status', Lang::get('chsett.selected_status'), $origStat, $this->statuses());
        $this->addSubmit('saveProp', Lang::get('dashboard.button_set'));
        $this->addReset('resetProp', Lang::get('dashboard.button_reset'));
        return $this;
    }
}
