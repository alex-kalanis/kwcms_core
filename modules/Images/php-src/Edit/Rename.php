<?php

namespace KWCMS\modules\Images\Edit;


use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_images\ImagesException;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_langs\Lang;
use kalanis\kw_modules\Output;
use kalanis\kw_notify\Notification;
use KWCMS\modules\Images\Forms;


/**
 * Class Rename
 * @package KWCMS\modules\Images\Edit
 * Images - Rename one
 */
class Rename extends AEdit
{
    /** @var string */
    protected $targetName = '';
    /** @var Forms\FileRenameForm|null */
    protected $renameForm = null;

    public function __construct()
    {
        parent::__construct();
        $this->renameForm = new Forms\FileRenameForm('fileNameForm');
    }

    public function run(): void
    {
        try {
            $this->initWhereDir(new SessionAdapter(), $this->inputs);
            $this->userDir->setUserPath($this->user->getDir());
            $this->userDir->process();

            $fileName = strval($this->getFromParam('name'));
            $this->renameForm->composeForm($fileName, '#');
            $this->renameForm->setInputs(new InputVarsAdapter($this->inputs));
            if ($this->renameForm->process()) {
                $newName = strval($this->renameForm->getControl('newName')->getValue());
                $libAction = $this->getLibFileAction();
                $this->checkExistence($libAction->getLibFiles(), $this->getWhereDir(), $fileName);
                $this->isProcessed = $libAction->renameFile(
                    $this->getWhereDir() . DIRECTORY_SEPARATOR . $fileName,
                    $newName
                );
                $this->targetName = $this->isProcessed ? $newName : $fileName ;
            }
        } catch (FormsException | ImagesException $ex) {
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
