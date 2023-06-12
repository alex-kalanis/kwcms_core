<?php

namespace KWCMS\modules\Files\AdminControllers\File;


use kalanis\kw_files\FilesException;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_forms\Interfaces\IMultiValue;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_langs\Lang;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;


/**
 * Class Delete
 * @package KWCMS\modules\Files\File
 * Delete content
 */
class Delete extends AFile
{
    protected function getFormAlias(): string
    {
        return 'deleteFileForm';
    }

    public function run(): void
    {
        $this->initWhereDir(new SessionAdapter(), $this->inputs);
        $this->userDir->setUserPath($this->getUserDir());

        try {
            $userPath = array_values($this->userDir->process()->getFullPath()->getArray());
            $fullPath = array_merge($userPath, Stuff::linkToArray($this->getWhereDir()));

            $this->tree->setStartPath($fullPath);
            $this->tree->wantDeep(false);
            $this->tree->setFilterCallback([$this, 'justFilesCallback']);
            $this->tree->process();

            $this->fileForm->composeDeleteFile($this->tree->getRoot());
            $this->fileForm->setInputs(new InputVarsAdapter($this->inputs));

            if ($this->fileForm->process()) {
                $entries = $this->fileForm->getControl('sourceName[]');
                if (!$entries instanceof IMultiValue) {
                    throw new FilesException(Lang::get('files.error.must_contain_files'));
                }
                if ('yes' != $this->fileForm->getControl('targetPath')->getValue()) {
                    return;
                }
                $actionLib = $this->getLibAction();
                foreach ($entries->getValues() as $item) {
                    $this->processed[$item] = $actionLib->deleteFile($item);
                }
                $this->tree->process();
                $this->fileForm->composeDeleteFile($this->tree->getRoot()); // again, changes in tree
            }
        } catch (FilesException | FormsException | PathsException $ex) {
            $this->error = $ex;
        }
    }

    protected function getFormTitleLangKey(): string
    {
        return 'files.file.delete';
    }

    protected function getSuccessLangKey(): string
    {
        return 'files.file.deleted';
    }

    protected function getFailureLangKey(): string
    {
        return 'files.file.not_deleted';
    }

    protected function getTitleLangKey(): string
    {
        return 'files.file.delete.short';
    }
}
