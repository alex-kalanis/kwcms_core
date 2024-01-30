<?php

namespace KWCMS\modules\Personal\AdminControllers;


use kalanis\kw_accounts\AccountsException;
use kalanis\kw_accounts\Interfaces;
use kalanis\kw_auth\Auth;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_forms\Exceptions\RenderException;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_modules\Output;
use kalanis\kw_notify\Notification;
use KWCMS\modules\Core\Interfaces\Modules\IHasTitle;
use KWCMS\modules\Core\Libs\AAuthModule;
use KWCMS\modules\Personal\Lib;
use KWCMS\modules\Personal\Templates;


/**
 * Class Dashboard
 * @package KWCMS\modules\Personal\AdminControllers
 * Site's users - personal properties
 */
class Dashboard extends AAuthModule implements IHasTitle
{
    use Templates\TModuleTemplate;

    /** @var Interfaces\IAuthCert|null */
    protected $libUsers = null;
    /** @var Interfaces\IProcessAccounts|null */
    protected $libAccounts = null;
    /** @var Interfaces\IUserCert|Interfaces\IUser|null */
    protected $editUser = null;
    /** @var Lib\FormProps|null */
    protected $form = null;
    /** @var bool */
    protected $isProcessed = false;

    /**
     * @param mixed ...$constructParams
     * @throws LangException
     */
    public function __construct(...$constructParams)
    {
        $this->initTModuleTemplate();
        $this->libUsers = Auth::getAuth() instanceof Interfaces\IAuthCert
            ? Auth::getAuth()
            : (
                Auth::getAccounts() instanceof Interfaces\IAuthCert
                    ? Auth::getAccounts()
                    : null
            );
        $this->libAccounts = Auth::getAccounts();
        $this->form = new Lib\FormProps();
    }

    public function allowedAccessClasses(): array
    {
        return [Interfaces\IProcessClasses::CLASS_MAINTAINER, Interfaces\IProcessClasses::CLASS_ADMIN, Interfaces\IProcessClasses::CLASS_USER, ];
    }

    public function run(): void
    {
        try {
            $this->editUser = (($this->user instanceof Interfaces\IUserCert) && $this->libUsers)
                ? $this->libUsers->getCertData($this->user->getAuthName())
                : $this->user
            ;
            $this->form->composeForm($this->editUser);
            if ($this->editUser instanceof Interfaces\IUserCert) {
                $this->form->addCerts($this->editUser);
            }
            $this->form->setInputs(new InputVarsAdapter($this->inputs));
            if ($this->form->process()) {
                $values = $this->form->getValues();
                $this->editUser->setUserData(
                    $this->editUser->getAuthId(),
                    $values['loginName'],
                    $this->editUser->getGroup(),
                    $this->editUser->getClass(),
                    $this->editUser->getStatus(),
                    $values['displayName'],
                    $this->editUser->getDir()
                );
                $this->libAccounts->updateAccount($this->editUser);
                if (($this->editUser instanceof Interfaces\IUserCert) && $this->libUsers) {
                    $this->libUsers->updateCertData(
                        $this->editUser->getAuthName(),
                        $values['pubKey'],
                        $values['pubSalt']
                    );
                }
                $this->isProcessed = true;
            }

        } catch (AccountsException | FormsException $ex) {
            $this->error = $ex;
        }
    }

    /**
     * @throws RenderException
     * @return Output\AOutput
     */
    public function result(): Output\AOutput
    {
        return $this->isJson()
            ? $this->outJson()
            : $this->outHtml();
    }

    public function outHtml(): Output\AOutput
    {
        $out = new Output\Html();
        try {
            if ($this->error) {
                Notification::addError($this->error->getMessage());
            }
            if ($this->isProcessed) {
                Notification::addSuccess(Lang::get('personal.properties_updated'));
            }
            $editTmpl = new Templates\EditTemplate();
            if ($this->editUser instanceof Interfaces\IUserCert) {
                $certTmpl = new Templates\CertTemplate();
                $editTmpl->addCerts($certTmpl->setData($this->form)->render());
            }
            return $out->setContent($this->outModuleTemplate($editTmpl->setData($this->form)->render()));
        } catch ( FormsException $ex) {
            return $out->setContent($this->outModuleTemplate($ex->getMessage() . nl2br($ex->getTraceAsString())));
        }
    }

    /**
     * @throws RenderException
     * @return Output\AOutput
     */
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
