<?php

namespace KWCMS\modules\Texts\AdminControllers;


use kalanis\kw_auth\Interfaces\IAccessClasses;
use kalanis\kw_files\Access;
use kalanis\kw_files\FilesException;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_modules\AAuthModule;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Output;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stored;
use kalanis\kw_paths\Stuff;
use kalanis\kw_routed_paths\StoreRouted;
use kalanis\kw_tree\DataSources;
use kalanis\kw_tree\Interfaces\ITree;
use kalanis\kw_tree_controls\TWhereDir;
use kalanis\kw_user_paths\UserDir;
use KWCMS\modules\Admin\Shared\ArrayAdapter;
use KWCMS\modules\Texts\Lib;
use KWCMS\modules\Texts\TextsException;


/**
 * Class Dashboard
 * @package KWCMS\modules\Texts\AdminControllers
 * Site's text content - list available files in directory
 */
class Dashboard extends AAuthModule implements IModuleTitle
{
    use Lib\TModuleTemplate;
    use TWhereDir;

    /** @var UserDir */
    protected $userDir = null;
    /** @var ITree */
    protected $tree = null;
    /** @var Lib\NewFileForm|null */
    protected $newFileForm = null;
    /** @var Lib\OpenFileForm|null */
    protected $openFileForm = null;

    /**
     * @throws FilesException
     * @throws LangException
     * @throws PathsException
     */
    public function __construct()
    {
        $this->initTModuleTemplate(Stored::getPath(), StoreRouted::getPath());
        $this->tree = new DataSources\Files((new Access\Factory())->getClass(Stored::getPath()->getDocumentRoot() . Stored::getPath()->getPathToSystemRoot()));
        $this->userDir = new UserDir();
        $this->newFileForm = new Lib\NewFileForm('newFileForm');
        $this->openFileForm = new Lib\OpenFileForm('openFileForm');
    }

    public function allowedAccessClasses(): array
    {
        return [IAccessClasses::CLASS_MAINTAINER, IAccessClasses::CLASS_ADMIN, IAccessClasses::CLASS_USER, ];
    }

    public function run(): void
    {
        $this->initWhereDir(new SessionAdapter(), $this->inputs);
        $this->userDir->setUserPath($this->user->getDir());

        try {
            $userPath = array_values($this->userDir->process()->getFullPath()->getArray());
            $fullPath = array_merge($userPath, Stuff::linkToArray($this->getWhereDir()));

            $this->tree->setStartPath($fullPath);
            $this->tree->wantDeep(false);
            $this->tree->setFilterCallback([$this->getParams(), 'filterFiles']);
            $this->tree->process();

            $this->newFileForm->composeForm($this->links->linkVariant($this->getTargetEdit()));
            $this->openFileForm->composeForm($this->getWhereDir(), $this->tree->getRoot(), $this->links->linkVariant($this->getTargetEdit()));
        } catch (FilesException | PathsException | TextsException $ex) {
            $this->error = $ex;
        }
    }

    protected function getParams(): Lib\Params
    {
        return new Lib\Params();
    }

    protected function getTargetEdit(): string
    {
        return 'texts/edit';
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
        $page = new Lib\TextsTemplate();
        try {
            $page->setData($this->newFileForm, $this->openFileForm);
            return $out->setContent($this->outModuleTemplate($page->render()));
        } catch (TextsException | FormsException $ex) {
            $this->error = $ex;
        }
        return $out->setContent($this->outModuleTemplate($this->error->getMessage() . nl2br($this->error->getTraceAsString())));
    }

    public function outJson(): Output\AOutput
    {
        if ($this->error) {
            $out = new Output\JsonError();
            return $out->setContent($this->error->getCode(), $this->error->getMessage());
        } else {
            $transform = new ArrayAdapter();
            $out = new Output\Json();
            $out->setContent([
                'form_result' => 0,
                'form_errors' => [],
                'tree' => $transform->pack($this->tree->getRoot()),
            ]);
            return $out;
        }
    }

    public function getTitle(): string
    {
        return Lang::get('texts.page');
    }
}
