<?php

namespace KWCMS\modules\Images\AdminControllers\Edit;


use kalanis\kw_files\FilesException;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_images\ImagesException;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_langs\Lang;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;
use KWCMS\modules\Images\Forms;


/**
 * Class Rename
 * @package KWCMS\modules\Images\AdminControllers\Edit
 * Images - Rename one
 */
class Rename extends AEdit
{
    protected string $targetName = '';
    protected Forms\FileRenameForm $renameForm;

    public function __construct(...$constructParams)
    {
        parent::__construct(...$constructParams);
        $this->renameForm = new Forms\FileRenameForm('fileNameForm');
    }

    public function run(): void
    {
        $this->initWhereDir(new SessionAdapter(), $this->inputs);
        $this->userDir->setUserPath($this->user->getDir());

        try {
            $userPath = array_filter(array_values($this->userDir->process()->getFullPath()->getArray()));
            $currentPath = array_filter(Stuff::linkToArray($this->getWhereDir()));

            $fileName = strval($this->getFromParam('name'));
            $this->renameForm->composeForm($fileName, '#');
            $this->renameForm->setInputs(new InputVarsAdapter($this->inputs));
            if ($this->renameForm->process()) {
                $newName = strval($this->renameForm->getControl('newName')->getValue());
                $libAction = $this->getLibFileAction($this->constructParams, $userPath, $currentPath);
                $this->checkExistence($libAction->getLibImage(), array_merge($userPath, $currentPath), $fileName);
                $this->isProcessed = $libAction->renameFile(
                    $this->getWhereDir() . DIRECTORY_SEPARATOR . $fileName,
                    $newName
                );
                $this->targetName = $this->isProcessed ? $newName : $fileName ;
            }
        } catch (FormsException | ImagesException | FilesException | PathsException $ex) {
            $this->error = $ex;
        }
    }

    protected function getSuccessTitle(): string
    {
        return Lang::get('images.renamed');
    }

    protected function getTargetForward(): string
    {
        return 'images/edit?name=' . $this->targetName;
    }
}
