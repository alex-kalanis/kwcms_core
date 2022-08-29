<?php

namespace KWCMS\modules\Personal;


use kalanis\kw_auth\Auth;
use kalanis\kw_auth\AuthException;
use kalanis\kw_auth\Interfaces;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_langs\Lang;
use kalanis\kw_locks\LockException;
use kalanis\kw_modules\AAuthModule;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Output;
use kalanis\kw_notify\Notification;
use kalanis\kw_rules\Exceptions\RuleException;


/**
 * Class Pass
 * @package KWCMS\modules\Personal
 * Site's users - personal password
 */
class Pass extends AAuthModule implements IModuleTitle
{
    use Templates\TModuleTemplate;

    /** @var Interfaces\IAuth|null */
    protected $libAuth = null;
    /** @var Interfaces\IAccessAccounts|null */
    protected $libAccount = null;
    /** @var Lib\FormPass|null */
    protected $form = null;
    /** @var bool */
    protected $isProcessed = false;

    public function __construct()
    {
        $this->initTModuleTemplate();
        $this->libAuth = Auth::getAuth();
        $this->libAccount = Auth::getAccounts();
        $this->form = new Lib\FormPass();
    }

    public function allowedAccessClasses(): array
    {
        return [Interfaces\IAccessClasses::CLASS_MAINTAINER, Interfaces\IAccessClasses::CLASS_ADMIN, Interfaces\IAccessClasses::CLASS_USER, ];
    }

    public function run(): void
    {
        try {
            $this->form->composeForm();
            $this->form->setInputs(new InputVarsAdapter($this->inputs));
            if ($this->form->process()) {
                $values = $this->form->getValues();
                if ($this->libAuth->authenticate($this->user->getAuthName(), ['password' => $values['currentPass']])) {
                    $this->libAccount->updatePassword($this->user->getAuthName(), $values['newPass']);
                    $this->isProcessed = true;
                }
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
        $out = new Output\Html();
        try {
            if ($this->error) {
                Notification::addError($this->error->getMessage());
            }
            if ($this->isProcessed) {
                Notification::addSuccess(Lang::get('personal.password_updated'));
            }
            $editTmpl = new Templates\PassTemplate();
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
