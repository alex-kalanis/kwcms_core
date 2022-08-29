<?php

namespace KWCMS\modules\Chsett\User;


use kalanis\kw_auth\AuthException;
use kalanis\kw_auth\Data\FileCertUser;
use kalanis\kw_auth\Data\FileUser;
use kalanis\kw_auth\Interfaces\IUserCert;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_langs\Lang;
use kalanis\kw_locks\LockException;
use kalanis\kw_rules\Exceptions\RuleException;


/**
 * Class Add
 * @package KWCMS\modules\Chsett\User
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
                $this->editUser->setData(
                    0,
                    $values['name'],
                    $values['group'],
                    $values['class'],
                    $values['desc'],
                    $values['dir']
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

        } catch (AuthException | FormsException | LockException | RuleException $ex) {
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
