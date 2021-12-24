<?php

namespace KWCMS\modules\Chsett\Group;


use kalanis\kw_auth\AuthException;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_langs\Lang;
use kalanis\kw_locks\LockException;
use kalanis\kw_rules\Exceptions\RuleException;


/**
 * Class Edit
 * @package KWCMS\modules\Chsett\Group
 * Site's groups - edit one
 */
class Edit extends AGroups
{
    public function run(): void
    {
        try {
            $groupId = intval(strval($this->getFromParam('id')));
            $this->group = $this->libAuth->getGroupDataOnly($groupId);
            if (empty($this->group)) {
                throw new AuthException(Lang::get('chsett.group_not_found', $groupId));
            }
            $this->form->composeForm($this->group);
            $this->form->setInputs(new InputVarsAdapter($this->inputs));
            if ($this->form->process()) {
                $values = $this->form->getValues();
                $this->group->setData(
                    $this->group->getGroupId(),
                    $values['name'],
                    $this->group->getGroupAuthorId(),
                    $values['desc']
                );
                $this->libAuth->updateGroup($this->group);
                $this->isProcessed = true;
                $this->redirect = true;
            }

        } catch (AuthException | FormsException | LockException | RuleException $ex) {
            $this->error = $ex;
        }
    }

    protected function getFormTitle(): string
    {
        return Lang::get('chsett.edit_group');
    }

    protected function getSuccessTitle(string $name): string
    {
        return Lang::get('chsett.group_updated', $name);
    }
}
