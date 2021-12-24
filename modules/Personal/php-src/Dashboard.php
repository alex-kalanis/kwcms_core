<?php

namespace KWCMS\modules\Personal;


use kalanis\kw_auth\Auth;
use kalanis\kw_auth\AuthException;
use kalanis\kw_auth\Interfaces\IAccessClasses;
use kalanis\kw_auth\Interfaces\IUser;
use kalanis\kw_auth\Interfaces\IUserCert;
use kalanis\kw_auth\Sources\Files;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_langs\Lang;
use kalanis\kw_locks\LockException;
use kalanis\kw_modules\AAuthModule;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Output;
use kalanis\kw_notify\Notification;
use kalanis\kw_rules\Exceptions\RuleException;
use KWCMS\modules\Admin\Shared;


/**
 * Class Dashboard
 * @package KWCMS\modules\Personal
 * Site's users - personal properties
 */
class Dashboard extends AAuthModule implements IModuleTitle
{
    use Templates\TModuleTemplate;

    /** @var Files|null */
    protected $libAuth = null;
    /** @var IUserCert|IUser|null */
    protected $editUser = null;
    /** @var Lib\FormProps|null */
    protected $form = null;
    /** @var bool */
    protected $isProcessed = false;

    public function __construct()
    {
        $this->initTModuleTemplate();
        $this->libAuth = Auth::getAuthenticator();
        $this->form = new Lib\FormProps();
    }

    public function allowedAccessClasses(): array
    {
        return [IAccessClasses::CLASS_MAINTAINER, IAccessClasses::CLASS_ADMIN, IAccessClasses::CLASS_USER, ];
    }

    public function run(): void
    {
        try {
            $this->editUser = $this->user instanceof IUserCert
                ? $this->libAuth->getCertData($this->user->getAuthName())
                : $this->user
            ;
            $this->form->composeForm($this->editUser);
            if ($this->editUser instanceof IUserCert) {
                $this->form->addCerts($this->editUser);
            }
            $this->form->setInputs(new InputVarsAdapter($this->inputs));
            if ($this->form->process()) {
                $values = $this->form->getValues();
                $this->editUser->setData(
                    $this->editUser->getAuthId(),
                    $values['loginName'],
                    $this->editUser->getGroup(),
                    $this->editUser->getClass(),
                    $values['displayName'],
                    $this->editUser->getDir()
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
            }

        } catch (AuthException | FormsException | LockException | RuleException $ex) {
            $this->error = $ex;
        }
    }

    public function result(): Output\AOutput
    {
        return $this->isJson()
            ? $this->outJson()
            : $this->outHtml();
    }

    public function outHtml(): Output\AOutput
    {
        $out = new Shared\FillHtml($this->user);
        try {
            if ($this->error) {
                Notification::addError($this->error->getMessage());
            }
            if ($this->isProcessed) {
                Notification::addSuccess(Lang::get('personal.properties_updated'));
            }
            $editTmpl = new Templates\EditTemplate();
            if ($this->editUser instanceof IUserCert) {
                $certTmpl = new Templates\CertTemplate();
                $editTmpl->addCerts($certTmpl->setData($this->form)->render());
            }
            return $out->setContent($this->outModuleTemplate($editTmpl->setData($this->form)->render()));
        } catch ( FormsException $ex) {
            return $out->setContent($this->outModuleTemplate($ex->getMessage() . nl2br($ex->getTraceAsString())));
        }
    }

    public function outJson(): Output\AOutput
    {
        if ($this->error) {
            $out = new Output\JsonError();
            return $out->setContent($this->error->getCode(), $this->error->getMessage());
        } elseif (!$this->form->isValid()) {
            $out = new Output\JsonError();
            return $out->setContent(1, $this->form->renderErrorsArray());
        } else {
            $out = new Output\Json();
            return $out->setContent(['Success']);
        }
    }

    public function getTitle(): string
    {
        return Lang::get('personal.page');
    }
}
