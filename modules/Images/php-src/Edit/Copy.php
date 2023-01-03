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
 * Class Copy
 * @package KWCMS\modules\Images\Edit
 * Images - Copy one
 */
class Copy extends AEdit
{
    /** @var string */
    protected $fileName = '';
    /** @var Forms\FileActionForm|null */
    protected $copyForm = null;
    /** @var Tree|null */
    protected $tree = null;

    public function __construct()
    {
        parent::__construct();
        $this->copyForm = new Forms\FileActionForm('fileCopyForm');
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

            $this->fileName = strval($this->getFromParam('name'));
            $this->copyForm->composeForm($this->tree->getTree(),'#');
            $this->copyForm->setInputs(new InputVarsAdapter($this->inputs));

            if ($this->copyForm->process()) {
                $libAction = $this->getLibFileAction();
                $this->checkExistence($libAction->getLibImage(), $this->getWhereDir(), $this->fileName);
                $this->isProcessed = $libAction->copyFile(
                    $this->getWhereDir() . DIRECTORY_SEPARATOR . $this->fileName,
                    strval($this->copyForm->getControl('where')->getValue())
                );
            }
        } catch (FormsException | ImagesException | FilesException $ex) {
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
