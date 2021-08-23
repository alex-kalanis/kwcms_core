<?php

namespace KWCMS\modules\Admin;


use kalanis\kw_auth\Interfaces\IAccessClasses;
use kalanis\kw_confs\Config;
use kalanis\kw_extras\UserDir;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_langs\Lang;
use kalanis\kw_modules\AAuthModule;
use kalanis\kw_modules\Output;
use kalanis\kw_tree\Adapters\ArrayAdapter;
use kalanis\kw_tree\Filters\DirFilter;
use kalanis\kw_tree\Tree;
use kalanis\kw_tree\TWhereDir;


/**
 * Class ChDir
 * @package KWCMS\modules\Admin
 * Admin Change working Directory
 * @link http://kwcms_core.lemp.test/web/ch-dir?
 */
class ChDir extends AAuthModule
{
    use TWhereDir;

    protected $userDir = null;
    protected $tree = null;
    protected $filter = null;
    protected $chDirForm = null;
    protected $processedForm = false;

    public function __construct()
    {
        Lang::load('Admin');
        $this->userDir = new UserDir(Config::getPath());
        $this->tree = new Tree(Config::getPath());
        $this->filter = new DirFilter();
        $this->chDirForm = new Forms\ChDirForm('chdirForm');
    }

    public function allowedAccessClasses(): array
    {
        return [IAccessClasses::CLASS_MAINTAINER, IAccessClasses::CLASS_ADMIN, IAccessClasses::CLASS_USER, ];
    }

    protected function run(): void
    {
        $this->initWhereDir(new SessionAdapter(), $this->inputs);
        $this->userDir->setUserPath($this->user->getDir());
        $this->userDir->process();
        $this->tree->canRecursive(true);
        $this->tree->startFromPath($this->userDir->getRealDir());
        $this->tree->process();
        $this->chDirForm->composeForm($this->getWhereDir(), $this->filter->filter($this->tree->getTree()));
        $inputVars = new InputVarsAdapter($this->inputs);
        $this->chDirForm->setInputs($inputVars);

        if ($this->chDirForm->process()) {
            $this->processedForm = true;
            $this->updateWhereDir($inputVars->offsetGet('dir'));
        }
    }

    protected function result(): Output\AOutput
    {
        if ($this->isJson()) {
            $transform = new ArrayAdapter();
            $out = new Output\Json();
            $out->setContent([
                'form_result' => intval($this->processedForm),
                'form_errors' => $this->chDirForm->renderErrorsArray(),
                'tree' => $transform->pack($this->tree->getTree()),
            ]);
            return $out;
        } else {
            return $this->htmlContent($this->chDirForm->render());
        }
    }

    protected function htmlContent(string $content): Output\AOutput
    {
        $out = new Shared\FillHtml($this->user);
        return $out->setContent($content);
    }
}
