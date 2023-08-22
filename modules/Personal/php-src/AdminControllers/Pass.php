<?php

namespace KWCMS\modules\Personal\AdminControllers;


use kalanis\kw_auth\Auth;
use kalanis\kw_auth_sources\AuthSourcesException;
use kalanis\kw_auth_sources\Interfaces;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_forms\Exceptions\RenderException;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_locks\LockException;
use kalanis\kw_modules\AAuthModule;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Output;
use kalanis\kw_notify\Notification;
use KWCMS\modules\Personal\Lib;
use KWCMS\modules\Personal\Templates;


/**
 * Class Pass
 * @package KWCMS\modules\Personal\AdminControllers
 * Site's users - personal password
 */
class Pass extends AAuthModule implements IModuleTitle
{
    use Templates\TModuleTemplate;

    /** @var Interfaces\IAuth|null */
    protected $libAuth = null;
    /** @var Interfaces\IWorkAccounts|null */
    protected $libAccount = null;
    /** @var Lib\FormPass|null */
    protected $form = null;
    /** @var bool */
    protected $isProcessed = false;

    /**
     * @throws LangException
     */
    public function __construct()
    {
        $this->initTModuleTemplate();
        $this->libAuth = Auth::getAuth();
        $this->libAccount = Auth::getAccounts();
        $this->form = new Lib\FormPass();
    }

    public function allowedAccessClasses(): array
    {
        return [Interfaces\IWorkClasses::CLASS_MAINTAINER, Interfaces\IWorkClasses::CLASS_ADMIN, Interfaces\IWorkClasses::CLASS_USER, ];
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
        } catch (AuthSourcesException | FormsException | LockException $ex) {
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
                Notification::addSuccess(Lang::get('personal.password_updated'));
            }
            $editTmpl = new Templates\PassTemplate();
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
