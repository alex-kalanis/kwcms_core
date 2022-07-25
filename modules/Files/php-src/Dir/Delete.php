<?php

namespace KWCMS\modules\Files\Dir;


use kalanis\kw_files\FilesException;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_forms\Interfaces\IMultiValue;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_langs\Lang;


/**
 * Class Delete
 * @package KWCMS\modules\Files\Dir
 * Delete content
 */
class Delete extends ADir
{
    protected function getFormAlias(): string
    {
        return 'deleteDirForm';
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
            $this->dirForm->composeDeleteDir($this->tree->getTree());
            $this->dirForm->setInputs(new InputVarsAdapter($this->inputs));

            if ($this->dirForm->process()) {
                $entries = $this->dirForm->getControl('sourceName[]');
                if (!$entries instanceof IMultiValue) {
                    throw new FilesException(Lang::get('files.error.must_contain_files'));
                }
                if ('yes' != $this->dirForm->getControl('targetPath')->getValue()) {
                    return;
                }
                $actionLib = $this->getLibDirAction();
                foreach ($entries->getValues() as $item) {
                    $this->processed[$item] = $actionLib->deleteDir($item);
                }
                $this->tree->process();
                $this->dirForm->composeDeleteDir($this->tree->getTree()); // again, changes in tree
            }
        } catch (FilesException | FormsException $ex) {
            $this->error = $ex;
        }
    }

    protected function getFormTitleLangKey(): string
    {
        return 'files.dir.delete';
    }

    protected function getSuccessLangKey(): string
    {
        return 'files.dir.deleted';
    }

    protected function getFailureLangKey(): string
    {
        return 'files.dir.not_deleted';
    }

    protected function getTitleLangKey(): string
    {
        return 'files.dir.delete.short';
    }
}
