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
 * Class Desc
 * @package KWCMS\modules\Images\AdminControllers\Edit
 * Images - Set one as primary for gallery
 */
class Desc extends AEdit
{
    /** @var string */
    protected $fileName = '';
    /** @var Forms\DescForm */
    protected $descForm = null;

    public function __construct()
    {
        parent::__construct();
        $this->descForm = new Forms\DescForm('fileDescForm');
    }

    public function run(): void
    {
        $this->initWhereDir(new SessionAdapter(), $this->inputs);
        $this->userDir->setUserPath($this->user->getDir());

        try {
            $userPath = array_values($this->userDir->process()->getFullPath()->getArray());
            $currentPath = Stuff::linkToArray($this->getWhereDir());

            $this->fileName = strval($this->getFromParam('name'));
            $libAction = $this->getLibFileAction($userPath, $currentPath);
            $this->checkExistence($libAction->getLibImage(), array_merge($userPath, $currentPath), $this->fileName);

            $this->descForm->composeForm($libAction->readDesc(
                $this->fileName
            ),'#');
            $this->descForm->setInputs(new InputVarsAdapter($this->inputs));

            if ($this->descForm->process()) {
                $libAction->updateDesc(
                    $this->fileName,
                    strval($this->descForm->getControl('description')->getValue())
                );
                $this->isProcessed = true;
            }
        } catch (FormsException | ImagesException | FilesException | PathsException $ex) {
            $this->error = $ex;
        }
    }

    protected function getSuccessTitle(): string
    {
        return Lang::get('images.desc_updated');
    }

    protected function getTargetForward(): string
    {
        return 'images/edit?name=' . $this->fileName;
    }
}
