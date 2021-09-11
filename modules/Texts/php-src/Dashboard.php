<?php

namespace KWCMS\modules\Texts;


use kalanis\kw_auth\Interfaces\IAccessClasses;
use kalanis\kw_confs\Config;
use kalanis\kw_extras\UserDir;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_langs\Lang;
use kalanis\kw_modules\AAuthModule;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Output;
use kalanis\kw_tree\Adapters\ArrayAdapter;
use kalanis\kw_tree\Tree;
use kalanis\kw_tree\TWhereDir;
use KWCMS\modules\Admin\Shared;


/**
 * Class Dashboard
 * @package KWCMS\modules\Texts
 * Site's text content - list available files in directory
 */
class Dashboard extends AAuthModule implements IModuleTitle
{
    use Lib\TModuleTemplate;
    use TWhereDir;

    /** @var UserDir|null */
    protected $userDir = null;
    /** @var Tree|null */
    protected $tree = null;
    /** @var Lib\NewFileForm|null */
    protected $newFileForm = null;
    /** @var Lib\OpenFileForm|null */
    protected $openFileForm = null;

    public function __construct()
    {
        $this->initTModuleTemplate(Config::getPath());
        $this->tree = new Tree(Config::getPath());
        $this->userDir = new UserDir(Config::getPath());
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
        $this->userDir->process();
        $this->tree->canRecursive(false);
        $this->tree->startFromPath($this->userDir->getHomeDir() . $this->getWhereDir());
        $this->tree->setFilterCallback([$this->getParams(), 'filterFiles']);
        $this->tree->process();
        $this->newFileForm->composeForm($this->links->linkVariant($this->getTargetEdit()));
        $this->openFileForm->composeForm($this->getWhereDir(), $this->tree->getTree(), $this->links->linkVariant($this->getTargetEdit()));
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
        $out = new Shared\FillHtml($this->user);
        $page = new Lib\TextsTemplate();
        try {
            $page->setData($this->newFileForm, $this->openFileForm);
            return $out->setContent($this->outModuleTemplate($page->render()));
        } catch (TextsException | FormsException $ex) {
            $this->error = $ex;
        }
        return $out->setContent($this->outModuleTemplate($this->error->getMessage()));
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
                'tree' => $transform->pack($this->tree->getTree()),
            ]);
            return $out;
        }
    }

    public function getTitle(): string
    {
        return Lang::get('texts.page');
    }
}
