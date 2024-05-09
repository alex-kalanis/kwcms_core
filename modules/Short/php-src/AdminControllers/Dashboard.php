<?php

namespace KWCMS\modules\Short\AdminControllers;


use kalanis\kw_accounts\Interfaces\IProcessClasses;
use kalanis\kw_confs\ConfException;
use kalanis\kw_confs\Config;
use kalanis\kw_connect\core\ConnectException;
use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_files\Access\Factory;
use kalanis\kw_files\FilesException;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_mapper\MapperException;
use kalanis\kw_mapper\Search\Search;
use kalanis\kw_modules\Output;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;
use kalanis\kw_table\core\TableException;
use kalanis\kw_tree_controls\TWhereDir;
use kalanis\kw_user_paths\UserDir;
use KWCMS\modules\Core\Interfaces\Modules\IHasTitle;
use KWCMS\modules\Core\Libs\AAuthModule;
use KWCMS\modules\Core\Libs\FilesTranslations;
use KWCMS\modules\Short\Lib;
use KWCMS\modules\Short\ShortException;


/**
 * Class Dashboard
 * @package KWCMS\modules\Short\AdminControllers
 * Site's short messages - admin table
 */
class Dashboard extends AAuthModule implements IHasTitle
{
    use Lib\TModuleTemplate;
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
        $this->initTModuleTemplate();
        $this->userDir = new UserDir(new Lib\Translations());
        $this->files = (new Factory(new FilesTranslations()))->getClass($constructParams);
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
            $this->search = new Search($adapter->getRecord());
        } catch (ConfException | FilesException | FormsException | MapperException | PathsException | ShortException $ex) {
            $this->error = $ex;
        }
    }

    public function result(): Output\AOutput
    {
        $out = new Output\Html();
        $table = new Lib\MessageTable($this->inputs, $this->links);
        if ($this->search) {
            try {
                return $out->setContent($this->outModuleTemplate($table->prepareHtml($this->search)));
            } catch (ConnectException | FormsException | LangException | TableException $ex) {
                $this->error = $ex;
            }
        }

        if ($this->error) {
            return $out->setContent($this->outModuleTemplate($this->error->getMessage() . nl2br($this->error->getTraceAsString())));
        } else {
            return $out->setContent($this->outModuleTemplate(Lang::get('short.cannot_read')));
        }
    }

    public function getTitle(): string
    {
        return Lang::get('short.page');
    }
}
