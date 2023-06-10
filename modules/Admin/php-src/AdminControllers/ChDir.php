<?php

namespace KWCMS\modules\Admin\AdminControllers;


use kalanis\kw_auth\Interfaces\IAccessClasses;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_forms\Exceptions\RenderException;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_modules\AAuthModule;
use kalanis\kw_modules\Output;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stored;
use kalanis\kw_tree\DataSources;
use kalanis\kw_tree\Interfaces\ITree;
use kalanis\kw_tree\Traits\TVolumeDirs;
use kalanis\kw_tree_controls\TWhereDir;
use kalanis\kw_user_paths\UserDir;
use KWCMS\modules\Admin\Forms;
use KWCMS\modules\Admin\Shared\ArrayAdapter;


/**
 * Class ChDir
 * @package KWCMS\modules\Admin\AdminControllers
 * Admin Change working Directory
 * @link http://kwcms_core.lemp.test/web/ch-dir?
 */
abstract class ChDir extends AAuthModule
{
    use TWhereDir;
    use TVolumeDirs;

    /** @var UserDir */
    protected $userDir = null;
    /** @var ITree */
    protected $tree = null;
    /** @var Forms\ChDirForm|null */
    protected $chDirForm = null;
    /** @var bool */
    protected $processedForm = false;

    /**
     * @throws LangException
     */
    public function __construct()
    {
        Lang::load('Admin');
        $this->tree = new DataSources\Volume(Stored::getPath()->getDocumentRoot() . Stored::getPath()->getPathToSystemRoot());
        $this->chDirForm = new Forms\ChDirForm('chdirForm');
        $this->userDir = new UserDir();
    }

    public function allowedAccessClasses(): array
    {
        return [IAccessClasses::CLASS_MAINTAINER, IAccessClasses::CLASS_ADMIN, IAccessClasses::CLASS_USER, ];
    }

    /**
     * @throws FormsException
     * @throws PathsException
     */
    protected function run(): void
    {
        // read session data
        $this->initWhereDir(new SessionAdapter(), $this->inputs);
        // parse user info from passwd / session
        $this->userDir->setUserPath($this->user->getDir());
        // path to user as defined in passwd / session
        $this->tree->setStartPath(array_values($this->userDir->process()->getFullPath()->getArray()));
        // want only dirs
        $this->tree->setFilterCallback([$this, 'justDirsCallback']);
        // full tree
        $this->tree->wantDeep(true);
        $this->tree->process();
        $this->chDirForm->composeForm($this->getWhereDir(), $this->tree->getRoot());
        $inputVars = new InputVarsAdapter($this->inputs);
        $this->chDirForm->setInputs($inputVars);

        if ($this->chDirForm->process()) {
            $this->processedForm = true;
            $this->updateWhereDir($inputVars->offsetGet('dir'));
        }
    }

    /**
     * @throws RenderException
     * @return Output\AOutput
     */
    protected function result(): Output\AOutput
    {
        if ($this->isJson()) {
            $transform = new ArrayAdapter();
            $out = new Output\Json();
            $out->setContent([
                'form_result' => intval($this->processedForm),
                'form_errors' => $this->chDirForm->renderErrorsArray(),
                'tree' => $transform->pack($this->tree->getRoot()),
            ]);
            return $out;
        } else {
            return $this->htmlContent($this->chDirForm->render());
        }
    }

    protected function htmlContent(string $content): Output\AOutput
    {
        $out = new Output\Html();
        return $out->setContent($content);
    }
}
