<?php

namespace KWCMS\modules\Chsett\AdminControllers\Group;


use kalanis\kw_auth\AuthException;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_langs\Lang;
use kalanis\kw_locks\LockException;


/**
 * Class Edit
 * @package KWCMS\modules\Chsett\AdminControllers\Group
 * Site's groups - edit one
 */
class Edit extends AGroups
{
    public function run(): void
    {
        try {
            $groupId = intval(strval($this->getFromParam('id')));
            $this->group = $this->libAuthEditGroups->getGroupDataOnly($groupId);
            if (empty($this->group)) {
                throw new AuthException(Lang::get('chsett.group_not_found', $groupId));
            }
            $this->form->composeForm($this->group);
            $this->form->setInputs(new InputVarsAdapter($this->inputs));
            if ($this->form->process()) {
                $values = $this->form->getValues();
                $this->group->setGroupData(
                    $this->group->getGroupId(),
                    $values['name'],
                    $values['desc'],
                    $this->group->getGroupAuthorId(),
                    '' == $values['status'] ? null : intval($values['status']),
                    $this->group->getGroupParents()
                );
                $this->libAuthEditGroups->updateGroup($this->group);
                $this->isProcessed = true;
                $this->redirect = true;
            }

        } catch (AuthException | FormsException | LockException $ex) {
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
