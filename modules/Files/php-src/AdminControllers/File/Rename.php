<?php

namespace KWCMS\modules\Files\AdminControllers\File;


use kalanis\kw_files\FilesException;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;


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
        $this->userDir->setUserPath($this->getUserDir());

        try {
            $userPath = array_values($this->userDir->process()->getFullPath()->getArray());
            $workPath = Stuff::linkToArray($this->getWhereDir());

            $this->tree->setStartPath(array_merge($userPath, $workPath));
            $this->tree->wantDeep(false);
            $this->tree->setFilterCallback([$this, 'justFilesCallback']);
            $this->tree->process();

            $this->fileForm->composeRenameFile($this->tree->getRoot());
            $this->fileForm->setInputs(new InputVarsAdapter($this->inputs));

            if ($this->fileForm->process()) {
                $item = $this->fileForm->getControl('sourceName')->getValue();
                $this->processor->setUserPath($userPath)->setWorkPath($workPath);
                $this->processed[$item] = $this->processor->renameFile(
                    $item,
                    $this->fileForm->getControl('targetPath')->getValue()
                );
                $this->tree->process();
                $this->fileForm->composeRenameFile($this->tree->getRoot()); // again, changes in tree
            }
        } catch (FilesException | FormsException | PathsException $ex) {
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
