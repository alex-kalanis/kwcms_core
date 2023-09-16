<?php

namespace KWCMS\modules\Chsett\AdminControllers\Group;


use kalanis\kw_accounts\AccountsException;
use kalanis\kw_accounts\Data\FileGroup;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_langs\Lang;


/**
 * Class Add
 * @package KWCMS\modules\Chsett\AdminControllers\Group
 * Site's groups - add one
 */
class Add extends AGroups
{
    public function run(): void
    {
        try {
            $this->group = new FileGroup();
            $this->form->composeForm($this->group);
            $this->form->setInputs(new InputVarsAdapter($this->inputs));
            if ($this->form->process()) {
                $values = $this->form->getValues();
                $this->group->setGroupData(
                    0,
                    $values['name'],
                    $values['desc'],
                    $this->user->getAuthId(),
                    '' == $values['status'] ? null : intval($values['status']),
                    []
                );
                $this->isProcessed = $this->libGroups->createGroup($this->group);
                $this->redirect = true;
            }

        } catch (AccountsException | FormsException $ex) {
            $this->error = $ex;
        }
    }

    protected function getFormTitle(): string
    {
        return Lang::get('chsett.add_group');
    }

    protected function getSuccessTitle(string $name): string
    {
        return Lang::get('chsett.group_added', $name);
    }
}
