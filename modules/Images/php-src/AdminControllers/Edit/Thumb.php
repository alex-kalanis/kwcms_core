<?php

namespace KWCMS\modules\Images\AdminControllers\Edit;


use kalanis\kw_files\FilesException;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_images\ImagesException;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_langs\Lang;
use kalanis\kw_mime\MimeException;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;
use KWCMS\modules\Images\Forms;


/**
 * Class Thumb
 * @package KWCMS\modules\Images\AdminControllers\Edit
 * Images - Regenerate thumb
 */
class Thumb extends AEdit
{
    protected string $fileName = '';
    protected Forms\FileThumbForm $thumbForm;

    public function __construct(...$constructParams)
    {
        parent::__construct(...$constructParams);
        $this->thumbForm = new Forms\FileThumbForm('fileThumbForm');
    }

    public function run(): void
    {
        $this->initWhereDir(new SessionAdapter(), $this->inputs);
        $this->userDir->setUserPath($this->user->getDir());

        try {
            $userPath = array_filter(array_values($this->userDir->process()->getFullPath()->getArray()));
            $currentPath = array_filter(Stuff::linkToArray($this->getWhereDir()));

            $this->fileName = strval($this->getFromParam('name'));
            $libAction = $this->getLibFileAction($this->constructParams, $userPath, $currentPath);
            $this->checkExistence($libAction->getLibImage(), array_merge($userPath, $currentPath), $this->fileName);

            $this->thumbForm->composeForm('#');
            $this->thumbForm->setInputs(new InputVarsAdapter($this->inputs));

            if ($this->thumbForm->process()) {
                $libAction->updateThumb($this->fileName);
                $this->isProcessed = true;
            }
        } catch (FormsException | ImagesException | FilesException | PathsException | MimeException $ex) {
            $this->error = $ex;
        }
    }

    protected function getSuccessTitle(): string
    {
        return Lang::get('images.thumb_recreated');
    }

    protected function getTargetForward(): string
    {
        return 'images/edit?name=' . $this->fileName;
    }
}
