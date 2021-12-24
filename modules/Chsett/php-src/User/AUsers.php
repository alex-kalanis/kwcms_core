<?php

namespace KWCMS\modules\Chsett\User;


use kalanis\kw_address_handler\Forward;
use kalanis\kw_address_handler\Sources\ServerRequest;
use kalanis\kw_auth\Auth;
use kalanis\kw_auth\Interfaces\IAccessClasses;
use kalanis\kw_auth\Interfaces\IUser;
use kalanis\kw_auth\Sources\Files;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_langs\Lang;
use kalanis\kw_modules\AAuthModule;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Output;
use kalanis\kw_notify\Notification;
use KWCMS\modules\Admin\Shared;
use KWCMS\modules\Chsett\Lib;
use KWCMS\modules\Chsett\Templates;


/**
 * Class AGroups
 * @package KWCMS\modules\Chsett\User
 * Site's groups - edit one
 */
abstract class AUsers extends AAuthModule implements IModuleTitle
{
    use Templates\TModuleTemplate;

    /** @var Files|null */
    protected $libAuth = null;
    /** @var IUser|null */
    protected $editUser = null;
    /** @var Lib\FormUsers|null */
    protected $form = null;
    /** @var Forward */
    protected $forward = null;
    /** @var bool */
    protected $isProcessed = false;
    /** @var bool */
    protected $redirect = false;

    public function __construct()
    {
        $this->initTModuleTemplate();
        $this->libAuth = Auth::getAuthenticator();
        $this->form = new Lib\FormUsers();
        $this->forward = new Forward();
        $this->forward->setSource(new ServerRequest());
    }

    public function allowedAccessClasses(): array
    {
        return [IAccessClasses::CLASS_MAINTAINER ];
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
                Notification::addSuccess($this->getSuccessTitle($this->editUser->getDisplayName()));
                if ($this->redirect) {
                    $this->forward->forward();
                    $this->forward->setForward($this->links->linkVariant('chsett/dashboard'));
                    $this->forward->forward();
                }
            }
            $editTmpl = new Templates\EditUserTemplate();
            $editTmpl->setData($this->form, $this->getFormTitle());
            if ($this->form->getControl('pass')) {
                $passTmpl = new Templates\EditPassTemplate();
                $editTmpl->addPass($passTmpl->setData($this->form)->render());
            }
            if ($this->form->getControl('pubKey')) {
                $certTmpl = new Templates\EditCertTemplate();
                $editTmpl->addCerts($certTmpl->setData($this->form)->render());
            }
            return $out->setContent($this->outModuleTemplate($editTmpl->render()));
        } catch ( FormsException $ex) {
            return $out->setContent($this->outModuleTemplate($ex->getMessage() . nl2br($ex->getTraceAsString())));
        }
    }

    abstract protected function getFormTitle(): string;

    abstract protected function getSuccessTitle(string $name): string;

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
        return Lang::get('chsett.page');
    }
}
