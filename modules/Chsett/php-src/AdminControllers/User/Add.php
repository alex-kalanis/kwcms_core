<?php

namespace KWCMS\modules\Chsett\AdminControllers\User;


use kalanis\kw_accounts\AccountsException;
use kalanis\kw_accounts\Data\FileCertUser;
use kalanis\kw_accounts\Data\FileUser;
use kalanis\kw_accounts\Interfaces\IUserCert;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_langs\Lang;


/**
 * Class Add
 * @package KWCMS\modules\Chsett\AdminControllers\User
 * Site's users - add one
 */
class Add extends AUsers
{
    public function run(): void
    {
        try {
            $this->editUser = ($this->user instanceof IUserCert) ? new FileCertUser() : new FileUser();
            $this->form->composeForm($this->editUser, $this->libGroups->readGroup(), $this->libClasses->readClasses());
            $this->form->wantPass();
            if ($this->editUser instanceof IUserCert) {
                $this->form->addCerts($this->editUser);
            }
            $this->form->setInputs(new InputVarsAdapter($this->inputs));
            if ($this->form->process()) {
                $values = $this->form->getValues();
                $this->editUser->setUserData(
                    0,
                    strval($values['name']),
                    strval($values['group']),
                    intval($values['class']),
                    '' == $values['status'] ? null : intval($values['status']),
                    strval($values['desc']),
                    strval($values['dir'])
                );
                if ($this->editUser instanceof IUserCert) {
                    $this->editUser->addCertInfo(
                        $values['pubKey'],
                        $values['pubSalt']
                    );
                }
                $this->libAccounts->createAccount($this->editUser, $values['pass']);
                $this->isProcessed = true;
                $this->redirect = true;
            }

        } catch (AccountsException | FormsException $ex) {
            $this->error = $ex;
        }
    }

    protected function getFormTitle(): string
    {
        return Lang::get('chsett.add_user');
    }

    protected function getSuccessTitle(string $name): string
    {
        return Lang::get('chsett.user_added', $name);
    }
}
