<?php

namespace KWCMS\modules\Images\Edit;


use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_images\ImagesException;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_langs\Lang;
use KWCMS\modules\Images\Forms;


/**
 * Class Primary
 * @package KWCMS\modules\Images\Edit
 * Images - Set one as primary for gallery
 */
class Primary extends AEdit
{
    /** @var string */
    protected $fileName = '';
    /** @var Forms\FileThumbForm|null */
    protected $primaryForm = null;

    public function __construct()
    {
        parent::__construct();
        $this->primaryForm = new Forms\FileThumbForm('filePrimaryForm');
    }

    public function run(): void
    {
        try {
            $this->initWhereDir(new SessionAdapter(), $this->inputs);
            $this->userDir->setUserPath($this->user->getDir());
            $this->userDir->process();

            $this->fileName = strval($this->getFromParam('name'));
            $this->checkExistence($this->getLibFileAction()->getLibFiles(), $this->getWhereDir(), $this->fileName);

            $this->primaryForm->composeForm('#');
            $this->primaryForm->setInputs(new InputVarsAdapter($this->inputs));

            if ($this->primaryForm->process()) {
                $this->isProcessed = $this->getLibDirAction()->updateThumb($this->getWhereDir() . DIRECTORY_SEPARATOR . $this->fileName);
            }
        } catch (FormsException | ImagesException $ex) {
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
