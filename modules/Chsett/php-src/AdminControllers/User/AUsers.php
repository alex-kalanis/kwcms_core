<?php

namespace KWCMS\modules\Chsett\AdminControllers\User;


use kalanis\kw_accounts\Interfaces;
use kalanis\kw_address_handler\Forward;
use kalanis\kw_address_handler\HandlerException;
use kalanis\kw_address_handler\Sources\ServerRequest;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_forms\Exceptions\RenderException;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_modules\Output;
use kalanis\kw_notify\Notification;
use kalanis\kw_notify\NotifyException;
use kalanis\kw_paths\PathsException;
use KWCMS\modules\Chsett\Lib;
use KWCMS\modules\Chsett\Templates;
use KWCMS\modules\Core\Interfaces\Modules\IHasTitle;
use KWCMS\modules\Core\Libs\AAuthModule;
use KWCMS\modules\Core\Libs\ExternalLink;


/**
 * Class AGroups
 * @package KWCMS\modules\Chsett\AdminControllers\User
 * Site's groups - edit one
 */
abstract class AUsers extends AAuthModule implements IHasTitle
{
    use Templates\TModuleTemplate;

    protected Interfaces\IProcessGroups $libGroups;
    protected Interfaces\IProcessClasses $libClasses;
    /** @var Interfaces\IProcessAccounts|Interfaces\IAuthCert */
    protected Interfaces\IProcessAccounts $libAccounts;
    protected ?Interfaces\IUser $editUser = null;
    protected Lib\FormUsers $form;
    protected Forward $forward;
    protected bool $isProcessed = false;
    protected bool $redirect = false;

//    /**
//     * @param mixed ...$constructParams
//     * @throws LangException
//     */
//    public function __construct(...$constructParams)
//    {
//        $this->initTModuleTemplate(new ExternalLink(StoreRouted::getPath()));
//        $this->libGroups = Auth::getGroups();
//        $this->libClasses = Auth::getClasses();
//        $this->libAccounts = Auth::getAccounts();
//        $this->form = new Lib\FormUsers();
//        $this->forward = new Forward();
//        $this->forward->setSource(new ServerRequest());
//    }

    /**
     * @param Interfaces\IProcessGroups $groups
     * @param Interfaces\IProcessClasses $classes
     * @param Interfaces\IProcessAccounts $accounts
     * @param Lib\FormUsers $form
     * @param Forward $forward
     * @param ServerRequest $request
     * @param ExternalLink $external
     * @throws LangException
     * @throws HandlerException
     */
    public function __construct(
        Interfaces\IProcessGroups $groups,
        Interfaces\IProcessClasses $classes,
        Interfaces\IProcessAccounts $accounts,
        Lib\FormUsers $form,
        Forward $forward,
        ServerRequest $request,
        ExternalLink $external
    ) {
        $this->initTModuleTemplate($external);
        $this->libGroups = $groups;
        $this->libClasses = $classes;
        $this->libAccounts = $accounts;
        $this->form = $form;
        $this->forward = $forward;
        $this->forward->setSource($request);
    }

    public function allowedAccessClasses(): array
    {
        return [Interfaces\IProcessClasses::CLASS_MAINTAINER ];
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
        $editTmpl = new Templates\EditUserTemplate();
        try {
            if ($this->error) {
                Notification::addError($this->error->getMessage());
            } else {
                if ($this->isProcessed) {
                    Notification::addSuccess($this->getSuccessTitle($this->editUser->getDisplayName()));
                    if ($this->redirect) {
                        $this->forward->forward();
                        $this->forward->setForward($this->links->linkVariant('chsett/dashboard'));
                        $this->forward->forward();
                    }
                }
                $editTmpl->setData($this->form, $this->getFormTitle());
                if ($this->form->getControl('pass')) {
                    $passTmpl = new Templates\EditPassTemplate();
                    $editTmpl->addPass($passTmpl->setData($this->form)->render());
                }
                if ($this->form->getControl('pubKey')) {
                    $certTmpl = new Templates\EditCertTemplate();
                    $editTmpl->addCerts($certTmpl->setData($this->form)->render());
                }
            }
            return $out->setContent($this->outModuleTemplate($editTmpl->render()));
        } catch ( FormsException | NotifyException | PathsException $ex) {
            return $out->setContent($this->outModuleTemplate($ex->getMessage() . nl2br($ex->getTraceAsString())));
        }
    }

    abstract protected function getFormTitle(): string;

    abstract protected function getSuccessTitle(string $name): string;

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
            return $out->setContentStructure(1, $this->form->renderErrorsArray());
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
