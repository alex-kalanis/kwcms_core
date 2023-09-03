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
 * Class Delete
 * @package KWCMS\modules\Images\AdminControllers\Edit
 * Images - delete one
 */
class Delete extends AEdit
{
    /** @var Forms\FileDeleteForm */
    protected $deleteForm = null;

    public function __construct(...$constructParams)
    {
        parent::__construct(...$constructParams);
        $this->deleteForm = new Forms\FileDeleteForm('fileDeleteForm');
    }

    public function run(): void
    {
        $this->initWhereDir(new SessionAdapter(), $this->inputs);
        $this->userDir->setUserPath($this->user->getDir());

        try {
            $userPath = array_values($this->userDir->process()->getFullPath()->getArray());
            $currentPath = Stuff::linkToArray($this->getWhereDir());

            $fileName = strval($this->getFromParam('name'));
            $this->deleteForm->composeForm('#');
            $this->deleteForm->setInputs(new InputVarsAdapter($this->inputs));
            if ($this->deleteForm->process()) {
                $libAction = $this->getLibFileAction($userPath, $currentPath);
                $this->checkExistence($libAction->getLibImage(), array_merge($userPath, $currentPath), $fileName);
                $this->isProcessed = $libAction->deleteFile($this->getWhereDir() . DIRECTORY_SEPARATOR . $fileName);
            }
        } catch (FormsException | ImagesException | FilesException | PathsException $ex) {
            $this->error = $ex;
        }
    }

    protected function getSuccessTitle(): string
    {
        return Lang::get('images.removed');
    }

    protected function getTargetForward(): string
    {
        return 'images/dashboard';
    }
}
