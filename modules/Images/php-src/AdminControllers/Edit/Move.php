<?php

namespace KWCMS\modules\Images\AdminControllers\Edit;


use kalanis\kw_address_handler\HandlerException;
use kalanis\kw_confs\ConfException;
use kalanis\kw_files\FilesException;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_images\ImagesException;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;
use kalanis\kw_tree\DataSources;
use kalanis\kw_tree\Interfaces\ITree;
use KWCMS\modules\Images\Forms;


/**
 * Class Move
 * @package KWCMS\modules\Images\AdminControllers\Edit
 * Images - Move one
 */
class Move extends AEdit
{
    protected Forms\FileActionForm $moveForm;
    protected ITree $tree;

    /**
     * @param mixed ...$constructParams
     * @throws FilesException
     * @throws PathsException
     * @throws ConfException
     * @throws LangException
     * @throws HandlerException
     */
    public function __construct(...$constructParams)
    {
        parent::__construct(...$constructParams);
        $this->moveForm = new Forms\FileActionForm('fileMoveForm');
        $this->tree = new DataSources\Files($this->files);
    }

    public function run(): void
    {
        $this->initWhereDir(new SessionAdapter(), $this->inputs);
        $this->userDir->setUserPath($this->user->getDir());

        try {
            $userPath = array_filter(array_values($this->userDir->process()->getFullPath()->getArray()));
            $currentPath = array_filter(Stuff::linkToArray($this->getWhereDir()));

            $this->tree->setStartPath(array_merge($userPath, $currentPath));
            $this->tree->wantDeep(true);
            $this->tree->setFilterCallback([$this, 'justDirsCallback']);
            $this->tree->process();

            $fileName = strval($this->getFromParam('name'));
            $this->moveForm->composeForm($this->tree->getRoot(),'#');
            $this->moveForm->setInputs(new InputVarsAdapter($this->inputs));

            if ($this->moveForm->process()) {
                $libAction = $this->getLibFileAction($this->constructParams, $userPath, $currentPath);
                $this->checkExistence($libAction->getLibImage(), array_merge($userPath, $currentPath), $fileName);
                $this->isProcessed = $libAction->moveFile(
                    $this->getWhereDir() . DIRECTORY_SEPARATOR . $fileName,
                    strval($this->moveForm->getControl('where')->getValue())
                );
            }
        } catch (FormsException | ImagesException | FilesException | PathsException $ex) {
            $this->error = $ex;
        }
    }

    protected function getSuccessTitle(): string
    {
        return Lang::get('images.moved');
    }

    protected function getTargetForward(): string
    {
        return 'images/dashboard';
    }
}
