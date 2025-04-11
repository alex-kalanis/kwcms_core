<?php

namespace KWCMS\modules\Chsett\AdminControllers\Group;


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
use KWCMS\modules\Chsett\Lib;
use KWCMS\modules\Chsett\Templates;
use KWCMS\modules\Core\Interfaces\Modules\IHasTitle;
use KWCMS\modules\Core\Libs\AAuthModule;
use KWCMS\modules\Core\Libs\ExternalLink;


/**
 * Class AGroups
 * @package KWCMS\modules\Chsett\AdminControllers\Group
 * Site's groups - edit one
 */
abstract class AGroups extends AAuthModule implements IHasTitle
{
    use Templates\TModuleTemplate;

    protected Interfaces\IProcessGroups $libGroups;
    protected ?Interfaces\IGroup $group = null;
    protected Lib\FormGroups $form;
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
//        $this->libAuthEditGroups = Auth::getGroups();
//        $this->form = new Lib\FormGroups();
//        $this->forward = new Forward();
//        $this->forward->setSource(new ServerRequest());
//    }

    /**
     * @param Interfaces\IProcessGroups $groups
     * @param Lib\FormGroups $form
     * @param Forward $forward
     * @param ServerRequest $request
     * @param ExternalLink $external
     * @throws LangException
     * @throws HandlerException
     */
    public function __construct(
        Interfaces\IProcessGroups $groups,
        Lib\FormGroups $form,
        Forward $forward,
        ServerRequest $request,
        ExternalLink $external
    ) {
        $this->initTModuleTemplate($external);
        $this->libGroups = $groups;
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
        $editTmpl = new Templates\EditGroupTemplate();
        try {
            if ($this->error) {
                Notification::addError($this->error->getMessage());
            } else {
                if ($this->isProcessed) {
                    Notification::addSuccess($this->getSuccessTitle($this->group->getGroupName()));
                    if ($this->redirect) {
                        $this->forward->forward();
                        $this->forward->setForward($this->links->linkVariant('chsett/groups'));
                        $this->forward->forward();
                    }
                }
                $editTmpl->setData($this->form, $this->getFormTitle());
            }
            return $out->setContent($this->outModuleTemplate($editTmpl->render()));
        } catch ( FormsException $ex) {
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
