<?php

namespace KWCMS\modules\Files\Dir;


use kalanis\kw_files\FilesException;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_input\Simplified\SessionAdapter;


/**
 * Class Create
 * @package KWCMS\modules\Files\Dir
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
        try {
            $this->userDir->setUserPath($this->user->getDir());
            $this->userDir->process();

            $this->dirForm->composeCreateDir();
            $this->dirForm->setInputs(new InputVarsAdapter($this->inputs));

            if ($this->dirForm->process()) {
                $item = $this->dirForm->getControl('targetPath')->getValue();
                $this->processed[$item] = $this->getLibDirAction()->createDir($item);
                $this->dirForm->composeCreateDir(); // again, changes in tree
            }
        } catch (FilesException | FormsException $ex) {
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
