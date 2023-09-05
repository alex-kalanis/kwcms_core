<?php

namespace KWCMS\modules\Images\AdminControllers;


use kalanis\kw_accounts\Interfaces\IProcessClasses;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Access;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_forms\Exceptions\RenderException;
use kalanis\kw_images\ImagesException;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_modules\Output;
use kalanis\kw_notify\Notification;
use kalanis\kw_paths\Interfaces\IPaths;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;
use kalanis\kw_tree\DataSources;
use kalanis\kw_tree\Interfaces\ITree;
use kalanis\kw_tree\Traits\TFilesDirs;
use kalanis\kw_tree_controls\TWhereDir;
use kalanis\kw_user_paths\UserDir;
use KWCMS\modules\Core\Interfaces\Modules\IHasTitle;
use KWCMS\modules\Core\Libs\AAuthModule;
use KWCMS\modules\Core\Libs\FilesTranslations;
use KWCMS\modules\Images\Lib;
use KWCMS\modules\Images\Forms;
use KWCMS\modules\Images\Templates;


/**
 * Class MakeDir
 * @package KWCMS\modules\Images\AdminControllers
 * Directory creation in another path
 */
class MakeDir extends AAuthModule implements IHasTitle
{
    use Lib\TLibAction;
    use Templates\TModuleTemplate;
    use TWhereDir;
    use TFilesDirs;

    /** @var Forms\DirNewForm */
    protected $createForm = null;
    /** @var Access\CompositeAdapter */
    protected $files = null;
    /** @var UserDir */
    protected $userDir = null;
    /** @var ITree */
    protected $tree = null;
    /** @var bool */
    protected $processed = false;

    /**
     * @param mixed ...$constructParams
     * @throws FilesException
     * @throws LangException
     * @throws PathsException
     */
    public function __construct(...$constructParams)
    {
        $this->initTModuleTemplate();
        $this->createForm = new Forms\DirNewForm('dirNewForm');
        $this->files = (new Access\Factory(new FilesTranslations()))->getClass($constructParams);
        $this->tree = new DataSources\Files($this->files);
        $this->userDir = new UserDir(new Lib\Translations());
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

            $this->tree->setStartPath($userPath);
            $this->tree->wantDeep(true);
            $this->tree->setFilterCallback([$this, 'justDirsCallback']);
            $this->tree->process();

            $this->createForm->composeForm($this->tree->getRoot(),'#');
            $this->createForm->setInputs(new InputVarsAdapter($this->inputs));
            if ($this->createForm->process()) {
                $libAction = $this->getLibDirAction($this->files, $userPath, $currentPath);
                $this->processed = $libAction->createDir(
                    strval($this->createForm->getControl('where')->getValue()),
                    strval($this->createForm->getControl('name')->getValue())
                );
                if (!empty($this->createForm->getControl('into')->getValue())) {
                    $this->updateWhereDir(
                        strval($this->createForm->getControl('where')->getValue())
                        . IPaths::SPLITTER_SLASH
                        . strval($this->createForm->getControl('name')->getValue())
                    );
                }
            }
        } catch (ImagesException | FormsException | FilesException | PathsException $ex) {
            $this->error = $ex;
        }
    }

    /**
     * @throws RenderException
     * @return Output\AOutput
     */
    public function result(): Output\AOutput
    {
        return $this->isJson()
            ? $this->outJson()
            : $this->outHtml();
    }

    public function outHtml(): Output\AOutput
    {
        $out = new Output\Html();
        $page = new Templates\DirNewTemplate();
        if ($this->error) {
            Notification::addError($this->error->getMessage());
        }
        try {
            if ($this->processed) {
                Notification::addSuccess(Lang::get('images.dir_created'));
            }
            return $out->setContent($this->outModuleTemplate($page->setData($this->createForm)->render()));
        } catch (FormsException $ex) {
            $this->error = $ex;
        }
        return $out->setContent($this->outModuleTemplate($this->error->getMessage() . nl2br($this->error->getTraceAsString())));
    }

    /**
     * @throws RenderException
     * @return Output\AOutput
     */
    public function outJson(): Output\AOutput
    {
        if ($this->error) {
            $out = new Output\JsonError();
            return $out->setContent($this->error->getCode(), $this->error->getMessage());
        } else {
            $out = new Output\Json();
            $out->setContent([
                'form_result' => intval($this->processed),
                'form_errors' => $this->createForm->renderErrorsArray(),
            ]);
            return $out;
        }
    }

    public function getTitle(): string
    {
        return Lang::get('images.page') . ' - ' . Lang::get('images.dir_create.short');
    }
}
