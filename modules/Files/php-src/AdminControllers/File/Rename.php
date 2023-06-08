<?php

namespace KWCMS\modules\Files\AdminControllers\File;


use kalanis\kw_files\FilesException;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_input\Simplified\SessionAdapter;


/**
 * Class Rename
 * @package KWCMS\modules\Files\AdminControllers\File
 * Rename content
 */
class Rename extends AFile
{
    protected function getFormAlias(): string
    {
        return 'renameFileForm';
    }

    public function run(): void
    {
        $this->initWhereDir(new SessionAdapter(), $this->inputs);
        try {
            $this->userDir->setUserPath($this->user->getDir());
            $this->userDir->process();

            $this->tree->startFromPath($this->userDir->getHomeDir() . $this->getWhereDir());
            $this->tree->canRecursive(false);
            $this->tree->setFilterCallback([$this, 'filterFiles']);
            $this->tree->process();
            $this->fileForm->composeRenameFile($this->tree->getTree());
            $this->fileForm->setInputs(new InputVarsAdapter($this->inputs));

            if ($this->fileForm->process()) {
                $item = $this->fileForm->getControl('sourceName')->getValue();
                $this->processed[$item] = $this->getLibAction()->renameFile(
                    $item,
                    $this->fileForm->getControl('targetPath')->getValue()
                );
                $this->tree->process();
                $this->fileForm->composeRenameFile($this->tree->getTree()); // again, changes in tree
            }
        } catch (FilesException | FormsException $ex) {
            $this->error = $ex;
        }
    }

    protected function getFormTitleLangKey(): string
    {
        return 'files.file.rename';
    }

    protected function getSuccessLangKey(): string
    {
        return 'files.file.renamed';
    }

    protected function getFailureLangKey(): string
    {
        return 'files.file.not_renamed';
    }

    protected function getTitleLangKey(): string
    {
        return 'files.file.rename.short';
    }
}
