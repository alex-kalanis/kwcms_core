<?php

namespace KWCMS\modules\Short\ApiControllers;


use kalanis\kw_accounts\Interfaces\IProcessClasses;
use kalanis\kw_confs\ConfException;
use kalanis\kw_confs\Config;
use kalanis\kw_connect\core\ConnectException;
use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_files\Access\Factory;
use kalanis\kw_files\FilesException;
use kalanis\kw_forms\Adapters\ArrayAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_mapper\MapperException;
use kalanis\kw_mapper\Search\Search;
use kalanis\kw_modules\ModuleException;
use kalanis\kw_modules\Output;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;
use kalanis\kw_table\core\TableException;
use kalanis\kw_tree_controls\TWhereDir;
use kalanis\kw_user_paths\UserDir;
use KWCMS\modules\Core\Libs\AApiAuthModule;
use KWCMS\modules\Core\Libs\FilesTranslations;
use KWCMS\modules\Short\Lib;
use KWCMS\modules\Short\ShortException;


/**
 * Class Listing
 * @package KWCMS\modules\Short\ApiControllers
 * Site's short messages - list table
 */
class Listing extends AApiAuthModule
{
    use TWhereDir;

    /** @var Search */
    protected ?Search $search = null;
    protected UserDir $userDir;
    protected CompositeAdapter $files;
    /** @var MapperException|ConnectException|null */
    protected $error = null;

    /**
     * @param mixed ...$constructParams
     * @throws ConfException
     * @throws FilesException
     * @throws LangException
     * @throws PathsException
     */
    public function __construct(...$constructParams)
    {
        Config::load('Short');
        Lang::load('Short');
        Lang::load('Admin');
        $this->whereConst = 'target';
        $this->userDir = new UserDir(new Lib\Translations());
        $this->files = (new Factory(new FilesTranslations()))->getClass($constructParams);
    }

    public function allowedAccessClasses(): array
    {
        return [IProcessClasses::CLASS_MAINTAINER, IProcessClasses::CLASS_ADMIN, IProcessClasses::CLASS_USER, ];
    }

    public function run(): void
    {
        $this->initWhereDir(new ArrayAdapter([]), $this->inputs); // not from session data
        $this->userDir->setUserPath($this->user->getDir());

        try {
            $userPath = array_values($this->userDir->process()->getFullPath()->getArray());
            $currentPath = Stuff::linkToArray($this->getWhereDir());

            $adapter = new Lib\MessageAdapter($this->files, array_merge($userPath, $currentPath));
            $this->search = new Search($adapter->getRecord());
        } catch (ConfException | FilesException | FormsException | MapperException | PathsException | ShortException $ex) {
            $this->error = $ex;
        }
    }

    public function result(): Output\AOutput
    {
        $out = new Output\Json();
        $table = new Lib\MessageTable($this->inputs);
        try {
            if ($this->search) {
                return $out->setContent($table->prepareJson($this->search));
            }
            $this->error = new ModuleException('No table found in preset directory');
        } catch (ConnectException | TableException | FormsException $ex) {
            $this->error = $ex;
        }

        if ($this->error) {
            $out = new Output\JsonError();
            return $out->setContent($this->error->getCode(), $this->error->getMessage());
        } else {
            return $out->setContent([Lang::get('short.cannot_read')]);
        }
    }
}
