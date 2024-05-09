<?php

namespace KWCMS\modules\Files\AdminControllers\Dir;


use kalanis\kw_files\FilesException;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;


/**
 * Class Klone
 * @package KWCMS\modules\Files\AdminControllers\Dir
 * Clone selected dir to new one in current dir
 */
class Klone extends ADir
{
    protected function getFormAlias(): string
    {
        return 'cloneDirForm';
    }

    public function run(): void
    {
        $this->initWhereDir(new SessionAdapter(), $this->inputs);
        $this->userDir->setUserPath($this->getUserDir());

        try {
            $userPath = array_filter(array_values($this->userDir->process()->getFullPath()->getArray()));
            $workPath = array_filter(Stuff::linkToArray($this->getWhereDir()));

            $this->tree->setStartPath(array_merge($userPath, $workPath));
            $this->tree->wantDeep(false);
            $this->tree->setFilterCallback([$this, 'justDirsCallback']);
            $this->tree->process();

            $this->dirForm->composeRenameDir($this->tree->getRoot());
            $this->dirForm->setInputs(new InputVarsAdapter($this->inputs));

            if ($this->dirForm->process()) {
                $item = $this->dirForm->getControl('sourceName')->getValue();
                $this->processed[$item] = $this->processor->setUserPath($userPath)->setWorkPath($workPath)->cloneDir(
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
        return 'files.dir.clone';
    }

    protected function getSuccessLangKey(): string
    {
        return 'files.dir.cloned';
    }

    protected function getFailureLangKey(): string
    {
        return 'files.dir.not_cloned';
    }

    protected function getTitleLangKey(): string
    {
        return 'files.dir.clone.short';
    }
}
