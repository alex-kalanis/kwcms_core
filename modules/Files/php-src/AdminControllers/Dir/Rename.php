<?php

namespace KWCMS\modules\Files\AdminControllers\Dir;


use kalanis\kw_files\FilesException;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;


/**
 * Class Rename
 * @package KWCMS\modules\Files\AdminControllers\Dir
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
        $this->userDir->setUserPath($this->getUserDir());

        try {
            $userPath = array_values($this->userDir->process()->getFullPath()->getArray());
            $workPath = Stuff::linkToArray($this->getWhereDir());

            $this->tree->setStartPath(array_merge($userPath, $workPath));
            $this->tree->wantDeep(false);
            $this->tree->setFilterCallback([$this, 'justDirsCallback']);
            $this->tree->process();

            $this->dirForm->composeRenameDir($this->tree->getRoot());
            $this->dirForm->setInputs(new InputVarsAdapter($this->inputs));

            if ($this->dirForm->process()) {
                $item = $this->dirForm->getControl('sourceName')->getValue();
                $this->processed[$item] = $this->processor->setUserPath($userPath)->setWorkPath($workPath)->renameDir(
                    $item,
                    $this->dirForm->getControl('targetPath')->getValue()
                );
                $this->tree->process();
                $this->dirForm->composeRenameDir($this->tree->getRoot()); // again, changes in tree
            }
        } catch (FilesException | FormsException | PathsException $ex) {
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
