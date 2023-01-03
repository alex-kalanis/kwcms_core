<?php

namespace KWCMS\modules\Images\Edit;


use kalanis\kw_files\FilesException;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_images\ImagesException;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_langs\Lang;
use KWCMS\modules\Images\Forms;


/**
 * Class Delete
 * @package KWCMS\modules\Images\Edit
 * Images - delete one
 */
class Delete extends AEdit
{
    /** @var Forms\FileDeleteForm|null */
    protected $deleteForm = null;

    public function __construct()
    {
        parent::__construct();
        $this->deleteForm = new Forms\FileDeleteForm('fileDeleteForm');
    }

    public function run(): void
    {
        try {
            $this->initWhereDir(new SessionAdapter(), $this->inputs);
            $this->userDir->setUserPath($this->user->getDir());
            $this->userDir->process();

            $fileName = strval($this->getFromParam('name'));
            $this->deleteForm->composeForm('#');
            $this->deleteForm->setInputs(new InputVarsAdapter($this->inputs));
            if ($this->deleteForm->process()) {
                $libAction = $this->getLibFileAction();
                $this->checkExistence($libAction->getLibImage(), $this->getWhereDir(), $fileName);
                $this->isProcessed = $libAction->deleteFile($this->getWhereDir() . DIRECTORY_SEPARATOR . $fileName);
            }
        } catch (FormsException | ImagesException | FilesException $ex) {
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
