<?php

namespace KWCMS\modules\Short\AdminControllers;


use kalanis\kw_accounts\Interfaces\IProcessClasses;
use kalanis\kw_address_handler\Forward;
use kalanis\kw_address_handler\HandlerException;
use kalanis\kw_address_handler\Sources\ServerRequest;
use kalanis\kw_confs\ConfException;
use kalanis\kw_confs\Config;
use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_files\Access\Factory;
use kalanis\kw_files\FilesException;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_mapper\MapperException;
use kalanis\kw_modules\Output;
use kalanis\kw_notify\Notification;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;
use kalanis\kw_tree_controls\TWhereDir;
use kalanis\kw_user_paths\UserDir;
use KWCMS\modules\Core\Interfaces\Modules\IHasTitle;
use KWCMS\modules\Core\Libs\AAuthModule;
use KWCMS\modules\Short\Lib;
use KWCMS\modules\Short\ShortException;


/**
 * Class Delete
 * @package KWCMS\modules\Short\AdminControllers
 * Site's short messages - delete record
 */
class Delete extends AAuthModule implements IHasTitle
{
    use Lib\TModuleTemplate;
    use TWhereDir;

    /** @var MapperException|null */
    protected $error = null;
    protected CompositeAdapter $files;
    protected UserDir $userDir;
    protected Forward $forward;
    protected bool $isProcessed = false;

    /**
     * @param mixed ...$constructParams
     * @throws ConfException
     * @throws FilesException
     * @throws HandlerException
     * @throws LangException
     * @throws PathsException
     */
    public function __construct(...$constructParams)
    {
        $this->initTModuleTemplate();
        Config::load('Short');
        $this->forward = new Forward();
        $this->forward->setSource(new ServerRequest());
        $this->userDir = new UserDir(new Lib\Translations());
        $this->files = (new Factory())->getClass($constructParams);
    }

    public function allowedAccessClasses(): array
    {
        return [IProcessClasses::CLASS_MAINTAINER, IProcessClasses::CLASS_ADMIN, IProcessClasses::CLASS_USER, ];
    }

    public function run(): void
    {
        $this->initWhereDir(new SessionAdapter(), $this->inputs);
        $this->userDir->setUserPath($this->user->getDir());

        try {
            $userPath = array_values($this->userDir->process()->getFullPath()->getArray());
            $currentPath = Stuff::linkToArray($this->getWhereDir());

            $adapter = new Lib\MessageAdapter($this->files, array_merge($userPath, $currentPath));
            $record = $adapter->getRecord();
            $record->id = strval($this->getFromParam('id'));
            $this->isProcessed = $record->delete();
        } catch (ConfException | FilesException | FormsException | MapperException | PathsException | ShortException $ex) {
            $this->error = $ex;
        }
    }

    public function result(): Output\AOutput
    {
        if ($this->error) {
            Notification::addError($this->error->getMessage());
        }
        if ($this->isProcessed) {
            Notification::addSuccess(Lang::get('short.removed'));
        }
        $this->forward->forward();
        $this->forward->setForward($this->links->linkVariant('short/dashboard'));
        $this->forward->forward();
        return new Output\Raw();
    }

    public function getTitle(): string
    {
        return Lang::get('short.page') . ' - ' . Lang::get('short.remove_record');
    }
}
