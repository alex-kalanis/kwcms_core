<?php

namespace KWCMS\modules\Files\AdminControllers\File;


use kalanis\kw_files\FilesException;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;


/**
 * Class Klone
 * @package KWCMS\modules\Files\File
 * Clone content to new file in current dir
 */
class Klone extends AFile
{
    protected function getFormAlias(): string
    {
        return 'cloneFileForm';
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
            $this->tree->setFilterCallback([$this, 'justFilesCallback']);
            $this->tree->process();

            $this->fileForm->composeRenameFile($this->tree->getRoot());
            $this->fileForm->setInputs(new InputVarsAdapter($this->inputs));

            if ($this->fileForm->process()) {
                $item = $this->fileForm->getControl('sourceName')->getValue();
                $this->processor->setUserPath($userPath)->setWorkPath($workPath);
                $this->processed[$item] = $this->processor->cloneFile(
                    $item,
                    $this->fileForm->getControl('targetPath')->getValue()
                );
                $this->tree->process();
                $this->fileForm->composeRenameFile($this->tree->getRoot()); // again, changes in tree
            } elseif ($errors = $this->fileForm->getValidatedErrors()) {
                $this->error = $this->parseErrors($errors);
            }
        } catch (FilesException | FormsException | PathsException $ex) {
            $this->error = $ex;
        }
    }

    protected function getFormTitleLangKey(): string
    {
        return 'files.file.clone';
    }

    protected function getSuccessLangKey(): string
    {
        return 'files.file.cloned';
    }

    protected function getFailureLangKey(): string
    {
        return 'files.file.not_cloned';
    }

    protected function getTitleLangKey(): string
    {
        return 'files.file.clone.short';
    }
}
