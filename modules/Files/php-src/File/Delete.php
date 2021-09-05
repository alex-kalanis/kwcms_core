<?php

namespace KWCMS\modules\Files\File;


use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_forms\Interfaces\IMultiValue;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_langs\Lang;
use KWCMS\modules\Files\FilesException;


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
        try {
            $this->userDir->setUserPath($this->user->getDir());
            $this->userDir->process();

            $this->tree->startFromPath($this->userDir->getHomeDir() . $this->getWhereDir());
            $this->tree->canRecursive(false);
            $this->tree->setFilterCallback([$this, 'filterFiles']);
            $this->tree->process();
            $this->fileForm->composeDeleteFile($this->tree->getTree());
            $this->fileForm->setInputs(new InputVarsAdapter($this->inputs));

            if ($this->fileForm->process()) {
                $entries = $this->fileForm->getControl('sourceName[]');
                if (!$entries instanceof IMultiValue) {
                    throw new FilesException(Lang::get('files.error.must_contain_files'));
                }
                $actionLib = $this->getLibFileAction();
                foreach ($entries->getValues() as $item) {
                    $this->processed[$item] = $actionLib->deleteFile($item);
//                    $this->fileForm->getControl('targetPath')->getValue()
                }
                $this->tree->process();
                $this->fileForm->composeDeleteFile($this->tree->getTree()); // again, changes in tree
                $this->fileForm->setInputs(new InputVarsAdapter($this->inputs));
                $this->fileForm->setSentValues();
            }
        } catch (FilesException | FormsException $ex) {
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
