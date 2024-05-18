<?php

namespace KWCMS\modules\Images\AdminControllers\Edit;


use kalanis\kw_accounts\Interfaces\IProcessClasses;
use kalanis\kw_address_handler\Forward;
use kalanis\kw_address_handler\HandlerException;
use kalanis\kw_address_handler\Sources\ServerRequest;
use kalanis\kw_confs\ConfException;
use kalanis\kw_confs\Config;
use kalanis\kw_files\Access;
use kalanis\kw_files\Access\Factory as files_factory;
use kalanis\kw_files\FilesException;
use kalanis\kw_images\Access\Factory as images_factory;
use kalanis\kw_images\ImagesException;
use kalanis\kw_langs\LangException;
use kalanis\kw_modules\Output;
use kalanis\kw_notify\Notification;
use kalanis\kw_paths\PathsException;
use kalanis\kw_tree\Traits\TFilesDirs;
use kalanis\kw_tree_controls\TWhereDir;
use kalanis\kw_user_paths\UserDir;
use KWCMS\modules\Core\Libs\AAuthModule;
use KWCMS\modules\Core\Libs\FilesTranslations;
use KWCMS\modules\Core\Libs\ImagesTranslations;
use KWCMS\modules\Images\Lib;
use KWCMS\modules\Images\Templates;


/**
 * Class AEdit
 * @package KWCMS\modules\Images\AdminControllers\Edit
 * Images - Actions in edit
 */
abstract class AEdit extends AAuthModule
{
    use Lib\TLibAction;
    use Lib\TLibExistence;
    use Templates\TModuleTemplate;
    use TWhereDir;
    use TFilesDirs;

    /** @var ImagesException|null */
    protected $error = null;
    protected UserDir $userDir;
    protected Access\CompositeAdapter $files;
    protected Forward $forward;
    protected bool $isProcessed = false;

    protected $constructParams = [];

    /**
     * @param mixed ...$constructParams
     * @throws ConfException
     * @throws FilesException
     * @throws LangException
     * @throws PathsException
     * @throws HandlerException
     */
    public function __construct(...$constructParams)
    {
        $this->initTModuleTemplate();
        Config::load('Images');
        $this->forward = new Forward();
        $this->forward->setSource(new ServerRequest());
        $this->files = (new files_factory(new FilesTranslations()))->getClass($constructParams);
        $this->userDir = new UserDir(new Lib\Translations());
        $this->initLibAction(new images_factory(
            $this->files,
            null,
            null,
            null,
            new ImagesTranslations()
        ));
        $this->constructParams = $constructParams;
    }

    public function allowedAccessClasses(): array
    {
        return [IProcessClasses::CLASS_MAINTAINER, IProcessClasses::CLASS_ADMIN, IProcessClasses::CLASS_USER, ];
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
