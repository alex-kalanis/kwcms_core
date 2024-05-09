<?php

namespace KWCMS\modules\Short\ApiControllers;


use kalanis\kw_accounts\Interfaces\IProcessClasses;
use kalanis\kw_confs\ConfException;
use kalanis\kw_confs\Config;
use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_files\Access\Factory;
use kalanis\kw_files\FilesException;
use kalanis\kw_forms\Adapters\ArrayAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_mapper\MapperException;
use kalanis\kw_modules\Output;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;
use kalanis\kw_tree_controls\TWhereDir;
use kalanis\kw_user_paths\UserDir;
use KWCMS\modules\Core\Libs\AApiAuthModule;
use KWCMS\modules\Short\Lib;
use KWCMS\modules\Short\ShortException;


/**
 * Class Delete
 * @package KWCMS\modules\Short\ApiControllers
 * Site's short messages - delete record
 */
class Delete extends AApiAuthModule
{
    use TWhereDir;

    /** @var MapperException|null */
    protected $error = null;
    protected CompositeAdapter $files;
    protected UserDir $userDir;
    protected bool $isProcessed = false;

    /**
     * @param mixed ...$constructParams
     * @throws ConfException
     * @throws FilesException
     * @throws LangException
     * @throws PathsException
     */
    public function __construct(...$constructParams)
    {
        Lang::load('Short');
        Lang::load('Admin');
        Config::load('Short');
        $this->whereConst = 'target';
        $this->userDir = new UserDir(new Lib\Translations());
        $this->files = (new Factory())->getClass($constructParams);
    }

    public function allowedAccessClasses(): array
    {
        return [IProcessClasses::CLASS_MAINTAINER, IProcessClasses::CLASS_ADMIN, IProcessClasses::CLASS_USER, ];
    }

    public function run(): void
    {
        $this->initWhereDir(new ArrayAdapter([]), $this->inputs);
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
            $out = new Output\JsonError();
            return $out->setContent($this->error->getCode(), $this->error->getMessage());
        } else {
            $out = new Output\Json();
            return $out->setContent(['Success', intval($this->isProcessed)]);
        }
    }
}
