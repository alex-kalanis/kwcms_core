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
 * Class Desc
 * @package KWCMS\modules\Images\Edit
 * Images - Set one as primary for gallery
 */
class Desc extends AEdit
{
    /** @var string */
    protected $fileName = '';
    /** @var Forms\DescForm|null */
    protected $descForm = null;

    public function __construct()
    {
        parent::__construct();
        $this->descForm = new Forms\DescForm('fileDescForm');
    }

    public function run(): void
    {
        try {
            $this->initWhereDir(new SessionAdapter(), $this->inputs);
            $this->userDir->setUserPath($this->user->getDir());
            $this->userDir->process();

            $this->fileName = strval($this->getFromParam('name'));
            $libAction = $this->getLibFileAction();
            $this->checkExistence($libAction->getLibImage(), $this->getWhereDir(), $this->fileName);

            $this->descForm->composeForm($libAction->readDesc(
                $this->getWhereDir() . DIRECTORY_SEPARATOR . $this->fileName
            ),'#');
            $this->descForm->setInputs(new InputVarsAdapter($this->inputs));

            if ($this->descForm->process()) {
                $libAction->updateDesc(
                    $this->getWhereDir() . DIRECTORY_SEPARATOR . $this->fileName,
                    strval($this->descForm->getControl('description')->getValue())
                );
                $this->isProcessed = true;
            }
        } catch (FormsException | ImagesException | FilesException $ex) {
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
