<?php

namespace KWCMS\modules\Images\AdminControllers\Edit;


use kalanis\kw_confs\ConfException;
use kalanis\kw_files\Access;
use kalanis\kw_files\FilesException;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_images\ImagesException;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stored;
use kalanis\kw_paths\Stuff;
use kalanis\kw_tree\DataSources;
use kalanis\kw_tree\Interfaces\ITree;
use KWCMS\modules\Images\Forms;


/**
 * Class Copy
 * @package KWCMS\modules\Images\AdminControllers\Edit
 * Images - Copy one
 */
class Copy extends AEdit
{
    /** @var string */
    protected $fileName = '';
    /** @var Forms\FileActionForm */
    protected $copyForm = null;
    /** @var ITree */
    protected $tree = null;

    /**
     * @throws ConfException
     * @throws FilesException
     * @throws LangException
     * @throws PathsException
     */
    public function __construct()
    {
        parent::__construct();
        $this->copyForm = new Forms\FileActionForm('fileCopyForm');
        $this->tree = new DataSources\Files((new Access\Factory())->getClass(Stored::getPath()->getDocumentRoot() . Stored::getPath()->getPathToSystemRoot()));
    }

    public function run(): void
    {
        $this->initWhereDir(new SessionAdapter(), $this->inputs);
        $this->userDir->setUserPath($this->user->getDir());

        try {
            $userPath = array_values($this->userDir->process()->getFullPath()->getArray());
            $currentPath = Stuff::linkToArray($this->getWhereDir());

            $this->tree->setStartPath(array_merge($userPath, $currentPath));
            $this->tree->wantDeep(true);
            $this->tree->setFilterCallback([$this, 'justDirsCallback']);
            $this->tree->process();

            $this->fileName = strval($this->getFromParam('name'));
            $this->copyForm->composeForm($this->tree->getRoot(),'#');
            $this->copyForm->setInputs(new InputVarsAdapter($this->inputs));

            if ($this->copyForm->process()) {
                $libAction = $this->getLibFileAction($userPath, $currentPath);
                $this->checkExistence($libAction->getLibImage(), array_merge($userPath, $currentPath), $this->fileName);
                $this->isProcessed = $libAction->copyFile(
                    $this->fileName,
                    strval($this->copyForm->getControl('where')->getValue())
                );
            }
        } catch (FormsException | ImagesException | FilesException | PathsException $ex) {
            $this->error = $ex;
        }
    }

    protected function getSuccessTitle(): string
    {
        return Lang::get('images.copied');
    }

    protected function getTargetForward(): string
    {
        return 'images/edit?name=' . $this->fileName;
    }
}
