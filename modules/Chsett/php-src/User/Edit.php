<?php

namespace KWCMS\modules\Chsett\User;


use kalanis\kw_auth\AuthException;
use kalanis\kw_auth\Interfaces\IUserCert;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_langs\Lang;
use kalanis\kw_locks\LockException;
use kalanis\kw_rules\Exceptions\RuleException;


/**
 * Class Edit
 * @package KWCMS\modules\Chsett\User
 * Site's users - edit one
 */
class Edit extends AUsers
{
    public function run(): void
    {
        try {
            $userName = strval($this->getFromParam('name'));
            $this->editUser = $this->user instanceof IUserCert
                ? $this->libAuth->getCertData($userName)
                : $this->libAuth->getDataOnly($userName)
            ;
            if (empty($this->editUser)) {
                throw new AuthException(Lang::get('chsett.user_not_found', $userName));
            }
            $this->form->composeForm($this->editUser, $this->libAuth->readGroup(), $this->libAuth->readClasses());
            if ($this->editUser instanceof IUserCert) {
                $this->form->addCerts($this->editUser);
            }
            $this->form->setInputs(new InputVarsAdapter($this->inputs));
            if ($this->form->process()) {
                $values = $this->form->getValues();
                $this->editUser->setData(
                    $this->editUser->getAuthId(),
                    $values['name'],
                    $values['group'],
                    $values['class'],
                    $values['desc'],
                    $values['dir']
                );
                $this->libAuth->updateAccount($this->editUser);
                if ($this->editUser instanceof IUserCert) {
                    $this->libAuth->updateCertKeys(
                        $this->editUser->getAuthName(),
                        $values['pubKey'],
                        $values['pubSalt']
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
