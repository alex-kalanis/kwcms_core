<?php

namespace KWCMS\modules\Chsett\AdminControllers\User;


use kalanis\kw_auth\AuthException;
use kalanis\kw_auth\Interfaces\IAuthCert;
use kalanis\kw_auth\Interfaces\IUserCert;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_langs\Lang;
use kalanis\kw_locks\LockException;
use kalanis\kw_rules\Exceptions\RuleException;


/**
 * Class Edit
 * @package KWCMS\modules\Chsett\AdminControllers\User
 * Site's users - edit one
 */
class Edit extends AUsers
{
    public function run(): void
    {
        try {
            $userName = strval($this->getFromParam('name'));
            $this->editUser = $this->user instanceof IUserCert
                ? $this->libAccounts->getCertData($userName)
                : $this->libAccounts->getDataOnly($userName)
            ;
            if (empty($this->editUser)) {
                throw new AuthException(Lang::get('chsett.user_not_found', $userName));
            }
            $this->form->composeForm($this->editUser, $this->libGroups->readGroup(), $this->libClasses->readClasses());
            if ($this->editUser instanceof IUserCert) {
                $this->form->addCerts($this->editUser);
            }
            $this->form->setInputs(new InputVarsAdapter($this->inputs));
            if ($this->form->process()) {
                $values = $this->form->getValues();
                $this->editUser->setUserData(
                    $this->editUser->getAuthId(),
                    strval($values['name']),
                    strval($values['group']),
                    intval($values['class']),
                    '' == $values['status'] ? null : intval($values['status']),
                    strval($values['desc']),
                    strval($values['dir'])
                );
                $this->libAccounts->updateAccount($this->editUser);
                if (($this->editUser instanceof IUserCert) && ($this->libAccounts instanceof IAuthCert)) {
                    $this->libAccounts->updateCertKeys(
                        $this->editUser->getAuthName(),
                        strval($values['pubKey']),
                        strval($values['pubSalt'])
                    );
                }
                $this->isProcessed = true;
                $this->redirect = true;
            }

        } catch (AuthException | FormsException | LockException | RuleException $ex) {
            $this->error = $ex;
        }
    }

    protected function getFormTitle(): string
    {
        return Lang::get('chsett.edit_user');
    }

    protected function getSuccessTitle(string $name): string
    {
        return Lang::get('chsett.user_updated', $name);
    }
}
