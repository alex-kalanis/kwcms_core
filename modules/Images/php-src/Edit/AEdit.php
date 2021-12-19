<?php

namespace KWCMS\modules\Images\Edit;


use kalanis\kw_address_handler\Forward;
use kalanis\kw_address_handler\Sources\ServerRequest;
use kalanis\kw_auth\Interfaces\IAccessClasses;
use kalanis\kw_confs\Config;
use kalanis\kw_extras\UserDir;
use kalanis\kw_images\ImagesException;
use kalanis\kw_modules\AAuthModule;
use kalanis\kw_modules\Output;
use kalanis\kw_notify\Notification;
use kalanis\kw_tree\TWhereDir;
use KWCMS\modules\Images\Lib;
use KWCMS\modules\Images\Templates;


/**
 * Class AEdit
 * @package KWCMS\modules\Images\Edit
 * Images - Actions in edit
 */
abstract class AEdit extends AAuthModule
{
    use Lib\TLibAction;
    use Lib\TLibExistence;
    use Lib\TLibFilters;
    use Templates\TModuleTemplate;
    use TWhereDir;

    /** @var ImagesException|null */
    protected $error = null;
    /** @var UserDir|null */
    protected $userDir = null;
    /** @var bool */
    protected $isProcessed = false;
    /** @var Forward */
    protected $forward = null;

    public function __construct()
    {
        $this->initTModuleTemplate();
        Config::load('Images');
        $this->forward = new Forward();
        $this->forward->setSource(new ServerRequest());
        $this->userDir = new UserDir(Config::getPath());
    }

    public function allowedAccessClasses(): array
    {
        return [IAccessClasses::CLASS_MAINTAINER, IAccessClasses::CLASS_ADMIN, IAccessClasses::CLASS_USER, ];
    }

    protected function getUserDir(): string
    {
        return $this->user->getDir();
    }

    public function result(): Output\AOutput
    {
        return $this->isJson()
            ? $this->outJson()
            : $this->outHtml();
    }

    public function outHtml(): Output\AOutput
    {
        if ($this->error) {
            Notification::addError($this->error->getMessage());
        }
        if ($this->isProcessed) {
            Notification::addSuccess($this->getSuccessTitle());
        }
        $this->forward->forward();
        $this->forward->setForward($this->links->linkVariant($this->getTargetForward()));
        $this->forward->forward();
        $this->forward->setForward($this->links->linkVariant('images/dashboard'));
        $this->forward->forward();
        return new Output\Raw();
    }

    abstract protected function getSuccessTitle(): string;

    abstract protected function getTargetForward(): string;

    public function outJson(): Output\AOutput
    {
        if ($this->error) {
            $out = new Output\JsonError();
            return $out->setContent($this->error->getCode(), $this->error->getMessage());
        } else {
            $out = new Output\Json();
            return $out->setContent(['Success']);
        }
    }
}
