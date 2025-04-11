<?php

namespace KWCMS\modules\Files\AdminControllers\Dir;


use kalanis\kw_files\FilesException;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;


/**
 * Class Create
 * @package KWCMS\modules\Files\AdminControllers\Dir
 * Create content
 */
class Create extends ADir
{
    protected function getFormAlias(): string
    {
        return 'createDirForm';
    }

    public function run(): void
    {
        $this->initWhereDir(new SessionAdapter(), $this->inputs);
        $this->userDir->setUserPath($this->getUserDir());

        try {
            $userPath = array_filter(array_values($this->userDir->process()->getFullPath()->getArray()));
            $workPath = array_filter(Stuff::linkToArray($this->getWhereDir()));

            $this->dirForm->composeCreateDir();
            $this->dirForm->setInputs(new InputVarsAdapter($this->inputs));

            if ($this->dirForm->process()) {
                $item = $this->dirForm->getControl('targetPath')->getValue();
                $this->processed[$item] = $this->processor->setUserPath($userPath)->setWorkPath($workPath)->createDir($item);
                $this->dirForm->composeCreateDir(); // again, changes in tree
            } elseif ($errors = $this->dirForm->getValidatedErrors()) {
                $this->error = $this->parseErrors($errors);
            }
        } catch (FilesException | FormsException | PathsException $ex) {
            $this->error = $ex;
        }
    }

    protected function getFormTitleLangKey(): string
    {
        return 'files.dir.create';
    }

    protected function getSuccessLangKey(): string
    {
        return 'files.dir.created';
    }

    protected function getFailureLangKey(): string
    {
        return 'files.dir.not_created';
    }

    protected function getTitleLangKey(): string
    {
        return 'files.dir.create.short';
    }
}
