<?php

namespace KWCMS\modules\Images\Edit;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Processing\Volume\ProcessDir;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_images\ImagesException;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_langs\Lang;
use kalanis\kw_paths\Stored;
use kalanis\kw_tree\Tree;
use KWCMS\modules\Images\Forms;


/**
 * Class Move
 * @package KWCMS\modules\Images\Edit
 * Images - Move one
 */
class Move extends AEdit
{
    /** @var Forms\FileActionForm|null */
    protected $moveForm = null;
    /** @var Tree|null */
    protected $tree = null;

    public function __construct()
    {
        parent::__construct();
        $this->moveForm = new Forms\FileActionForm('fileMoveForm');
        $this->tree = new Tree(Stored::getPath(), new ProcessDir());
    }

    public function run(): void
    {
        try {
            $this->initWhereDir(new SessionAdapter(), $this->inputs);
            $this->userDir->setUserPath($this->user->getDir());
            $this->userDir->process();

            $this->tree->canRecursive(true);
            $this->tree->startFromPath($this->userDir->getHomeDir());
            $this->tree->setFilterCallback([$this, 'filterDirs']);
            $this->tree->process();

            $fileName = strval($this->getFromParam('name'));
            $this->moveForm->composeForm($this->tree->getTree(),'#');
            $this->moveForm->setInputs(new InputVarsAdapter($this->inputs));

            if ($this->moveForm->process()) {
                $libAction = $this->getLibFileAction();
                $this->checkExistence($libAction->getLibImage(), $this->getWhereDir(), $fileName);
                $this->isProcessed = $libAction->moveFile(
                    $this->getWhereDir() . DIRECTORY_SEPARATOR . $fileName,
                    strval($this->moveForm->getControl('where')->getValue())
                );
            }
        } catch (FormsException | ImagesException | FilesException $ex) {
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
