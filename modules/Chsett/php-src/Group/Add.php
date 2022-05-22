<?php

namespace KWCMS\modules\Chsett\Group;


use kalanis\kw_auth\AuthException;
use kalanis\kw_auth\Data\FileGroup;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_langs\Lang;
use kalanis\kw_locks\LockException;
use kalanis\kw_rules\Exceptions\RuleException;


/**
 * Class Add
 * @package KWCMS\modules\Chsett\Group
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
                $this->group->setData(
                    0,
                    $values['name'],
                    $this->user->getAuthId(),
                    $values['desc']
                );
                $this->libAuth->createGroup($this->group);
                $this->isProcessed = true;
                $this->redirect = true;
            }

        } catch (AuthException | FormsException | LockException | RuleException $ex) {
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
