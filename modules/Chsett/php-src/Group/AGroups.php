<?php

namespace KWCMS\modules\Chsett\Group;


use kalanis\kw_address_handler\Forward;
use kalanis\kw_address_handler\Sources\ServerRequest;
use kalanis\kw_auth\Auth;
use kalanis\kw_auth\Interfaces\IAccessClasses;
use kalanis\kw_auth\Interfaces\IAccessGroups;
use kalanis\kw_auth\Interfaces\IGroup;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_langs\Lang;
use kalanis\kw_modules\AAuthModule;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Output;
use kalanis\kw_notify\Notification;
use KWCMS\modules\Chsett\Lib;
use KWCMS\modules\Chsett\Templates;


/**
 * Class AGroups
 * @package KWCMS\modules\Chsett\Group
 * Site's groups - edit one
 */
abstract class AGroups extends AAuthModule implements IModuleTitle
{
    use Templates\TModuleTemplate;

    /** @var IAccessGroups|null */
    protected $libAuthEditGroups = null;
    /** @var IGroup|null */
    protected $group = null;
    /** @var Lib\FormGroups|null */
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
        $this->libAuthEditGroups = Auth::getGroups();
        $this->form = new Lib\FormGroups();
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
        $out = new Output\Html();
        try {
            if ($this->error) {
                Notification::addError($this->error->getMessage());
            }
            if ($this->isProcessed) {
                Notification::addSuccess($this->getSuccessTitle($this->group->getGroupName()));
                if ($this->redirect) {
                    $this->forward->forward();
                    $this->forward->setForward($this->links->linkVariant('chsett/groups'));
                    $this->forward->forward();
                }
            }
            $editTmpl = new Templates\EditGroupTemplate();
            return $out->setContent($this->outModuleTemplate($editTmpl->setData($this->form, $this->getFormTitle())->render()));
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
