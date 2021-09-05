<?php

namespace KWCMS\modules\Files\Dir;


use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_input\Simplified\SessionAdapter;
use KWCMS\modules\Files\FilesException;


/**
 * Class Rename
 * @package KWCMS\modules\Files\Dir
 * Rename content
 */
class Rename extends ADir
{
    protected function getFormAlias(): string
    {
        return 'renameDirForm';
    }

    public function run(): void
    {
        $this->initWhereDir(new SessionAdapter(), $this->inputs);
        try {
            $this->userDir->setUserPath($this->user->getDir());
            $this->userDir->process();

            $this->tree->startFromPath($this->userDir->getHomeDir() . $this->getWhereDir());
            $this->tree->canRecursive(false);
            $this->tree->setFilterCallback([$this, 'filterDirs']);
            $this->tree->process();
            $this->dirForm->composeRenameDir($this->tree->getTree());
            $this->dirForm->setInputs(new InputVarsAdapter($this->inputs));

            if ($this->dirForm->process()) {
                $item = $this->dirForm->getControl('sourceName')->getValue();
                $this->processed[$item] = $this->getLibFileAction()->renameFile(
                    $item,
                    $this->dirForm->getControl('targetPath')->getValue()
                );
                $this->tree->process();
                $this->dirForm->composeRenameDir($this->tree->getTree()); // again, changes in tree
            }
        } catch (FilesException | FormsException $ex) {
            $this->error = $ex;
        }
    }

    protected function getFormTitleLangKey(): string
    {
        return 'files.dir.rename';
    }

    protected function getSuccessLangKey(): string
    {
        return 'files.dir.renamed';
    }

    protected function getFailureLangKey(): string
    {
        return 'files.dir.not_renamed';
    }

    protected function getTitleLangKey(): string
    {
        return 'files.dir.rename.short';
    }
}
