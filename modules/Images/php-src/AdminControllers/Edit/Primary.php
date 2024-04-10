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
 * Class Primary
 * @package KWCMS\modules\Images\AdminControllers\Edit
 * Images - Set one as primary for gallery
 */
class Primary extends AEdit
{
    protected string $fileName = '';
    protected Forms\FileThumbForm $primaryForm;

    public function __construct(...$constructParams)
    {
        parent::__construct(...$constructParams);
        $this->primaryForm = new Forms\FileThumbForm('filePrimaryForm');
    }

    public function run(): void
    {
        $this->initWhereDir(new SessionAdapter(), $this->inputs);
        $this->userDir->setUserPath($this->user->getDir());

        try {
            $userPath = array_filter(array_values($this->userDir->process()->getFullPath()->getArray()));
            $currentPath = array_filter(Stuff::linkToArray($this->getWhereDir()));

            $this->fileName = strval($this->getFromParam('name'));
            $libAction = $this->getLibFileAction($this->files, $userPath, $currentPath);
            $this->checkExistence($libAction->getLibImage(), array_merge($userPath, $currentPath), $this->fileName);

            $this->primaryForm->composeForm('#');
            $this->primaryForm->setInputs(new InputVarsAdapter($this->inputs));

            if ($this->primaryForm->process()) {
                $this->isProcessed = $this->getLibDirAction($this->files, $userPath, $currentPath)->updateThumb($this->fileName);
            }
        } catch (FormsException | ImagesException | FilesException | PathsException | MimeException $ex) {
            $this->error = $ex;
        }
    }

    protected function getSuccessTitle(): string
    {
        return Lang::get('images.set_as_primary');
    }

    protected function getTargetForward(): string
    {
        return 'images/edit?name=' . $this->fileName;
    }
}
