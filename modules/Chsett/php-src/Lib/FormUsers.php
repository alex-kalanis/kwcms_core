<?php

namespace KWCMS\modules\Chsett\Lib;


use kalanis\kw_auth\Interfaces\IGroup;
use kalanis\kw_auth\Interfaces\IUser;
use kalanis\kw_auth\Interfaces\IUserCert;
use kalanis\kw_forms\Controls;
use kalanis\kw_forms\Form;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_langs\Lang;
use kalanis\kw_rules\Exceptions\RuleException;
use kalanis\kw_rules\Interfaces\IRules;


/**
 * Class FormUsers
 * @package KWCMS\modules\Chsett\Lib
 * Edit user props
 * @property Controls\Text name
 * @property Controls\Text desc
 * @property Controls\Select group
 * @property Controls\Select class
 * @property Controls\Text dir
 * @property Controls\Password pass
 * @property Controls\Textarea pubKey
 * @property Controls\Text pubSalt
 * @property Controls\Submit saveProp
 * @property Controls\Reset resetProp
 */
class FormUsers extends Form
{
    /**
     * @param IUser $user
     * @param IGroup[] $groups
     * @param string[] $classes
     * @return $this
     * @throws RuleException
     */
    public function composeForm(IUser $user, array $groups, array $classes): self
    {
        $this->setMethod(IEntry::SOURCE_POST);
        $this->addText('name', Lang::get('chsett.user_name'), $user->getAuthName())
            ->addRule(IRules::IS_NOT_EMPTY, Lang::get('chsett.user_name_empty'));
        $this->addText('desc', Lang::get('chsett.user_desc'), $user->getDisplayName())
            ->addRule(IRules::IS_NOT_EMPTY, Lang::get('chsett.user_desc_empty'));
        $this->addSelect('group', Lang::get('chsett.selected_group'), $user->getGroup(), $this->formGroups($groups))
            ->addRule(IRules::IS_NOT_EMPTY, Lang::get('chsett.selected_group_empty'));
        $this->addSelect('class', Lang::get('chsett.selected_class'), $user->getClass(), $classes)
            ->addRule(IRules::IS_NOT_EMPTY, Lang::get('chsett.selected_class_empty'));
        $this->addText('dir', Lang::get('chsett.from_dir'), $user->getDir());
        $this->addSubmit('saveProp', Lang::get('dashboard.button_set'));
        $this->addReset('resetProp', Lang::get('dashboard.button_reset'));
        return $this;
    }

    protected function formGroups(array $groups): array
    {
        return array_combine(array_map([$this, 'getGroupKey'], $groups), array_map([$this, 'getGroupName'], $groups));
    }

    public function getGroupKey(IGroup $group): int
    {
        return $group->getGroupId();
    }

    public function getGroupName(IGroup $group): string
    {
        return $group->getGroupName();
    }

    /**
     * @return $this
     * @throws RuleException
     */
    public function wantPass(): self
    {
        $this->addPassword('pass', Lang::get('chsett.pass'))
            ->addRule(IRules::IS_NOT_EMPTY, Lang::get('chsett.pass_empty'));
        return $this;
    }

    public function addCerts(IUserCert $user): self
    {
        $this->addTextarea('pubKey', Lang::get('chsett.public_key'), $user->getPubKey(), ['rows' => 10, 'cols' => 70]);
        $this->addText('pubSalt', Lang::get('chsett.public_salt'), $user->getPubSalt());
        return $this;
    }
}
