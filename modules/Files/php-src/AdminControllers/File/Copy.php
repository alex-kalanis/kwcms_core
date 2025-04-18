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
 * Class Copy
 * @package KWCMS\modules\Files\File
 * Copy selected file to another dir
 */
class Copy extends AFile
{
    protected function getFormAlias(): string
    {
        return 'copyFileForm';
    }

    public function run(): void
    {
        $this->initWhereDir(new SessionAdapter(), $this->inputs);
        $this->userDir->setUserPath($this->getUserDir());

        try {
            $userPath = array_filter(array_values($this->userDir->process()->getFullPath()->getArray()));
            $workPath = array_filter(Stuff::linkToArray($this->getWhereDir()));

            $this->tree->setStartPath($userPath);
            $this->tree->wantDeep(true);
            $this->tree->setFilterCallback([$this, 'justDirsCallback']);
            $this->tree->process();
            $targetTree = $this->tree->getRoot();

            $this->tree->setStartPath(array_merge($userPath, $workPath));
            $this->tree->wantDeep(false);
            $this->tree->setFilterCallback([$this, 'justFilesCallback']);
            $this->tree->process();
            $sourceTree = $this->tree->getRoot();

            $this->fileForm->composeCopyFile($sourceTree, $targetTree);
            $this->fileForm->setInputs(new InputVarsAdapter($this->inputs));
            if ($this->fileForm->process()) {
                $entries = $this->fileForm->getControl('sourceName[]');
                if (!$entries instanceof IMultiValue) {
                    throw new FilesException(Lang::get('files.error.must_contain_files'));
                }
                $this->processor->setUserPath($userPath)->setWorkPath($workPath);
                foreach ($entries->getValues() as $item) {
                    $this->processed[$item] = $this->processor->copyFile(
                        $item,
                        $this->fileForm->getControl('targetPath')->getValue()
                    );
                }
                $this->tree->process();
                $sourceTree = $this->tree->getRoot();
                $this->fileForm->composeCopyFile($sourceTree, $targetTree); // again, changes in tree
                $this->fileForm->setInputs(new InputVarsAdapter($this->inputs));
                $this->fileForm->setSentValues();
            } elseif ($errors = $this->fileForm->getValidatedErrors()) {
                $this->error = $this->parseErrors($errors);
            }
        } catch (FilesException | FormsException | PathsException $ex) {
            $this->error = $ex;
        }
    }

    protected function getFormTitleLangKey(): string
    {
        return 'files.file.copy';
    }

    protected function getSuccessLangKey(): string
    {
        return 'files.file.copied';
    }

    protected function getFailureLangKey(): string
    {
        return 'files.file.not_copied';
    }

    protected function getTitleLangKey(): string
    {
        return 'files.file.copy.short';
    }
}
