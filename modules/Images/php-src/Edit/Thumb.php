<?php

namespace KWCMS\modules\Images\Edit;


use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_images\Files;
use kalanis\kw_images\ImagesException;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_langs\Lang;
use KWCMS\modules\Images\Forms;


/**
 * Class Thumb
 * @package KWCMS\modules\Images\Edit
 * Images - Regenerate thumb
 */
class Thumb extends AEdit
{
    /** @var string */
    protected $fileName = '';
    /** @var Forms\FileThumbForm|null */
    protected $thumbForm = null;

    public function __construct()
    {
        parent::__construct();
        $this->thumbForm = new Forms\FileThumbForm('fileThumbForm');
    }

    public function run(): void
    {
        try {
            $this->initWhereDir(new SessionAdapter(), $this->inputs);
            $this->userDir->setUserPath($this->user->getDir());
            $this->userDir->process();

            $fileName = strval($this->getFromParam('name'));
            $libFiles = $this->getLibFileAction()->getLibFiles();
            $this->checkExistence($libFiles, $this->getWhereDir(), $fileName);

            $this->thumbForm->composeForm('#');
            $this->thumbForm->setInputs(new InputVarsAdapter($this->inputs));

            if ($this->thumbForm->process()) {
                /** @var Files $libFiles */
                $libFiles->getLibThumb()->create($this->getWhereDir() . DIRECTORY_SEPARATOR . $fileName);
                $this->isProcessed = true;
            }
        } catch (FormsException | ImagesException $ex) {
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
