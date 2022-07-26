<?php

namespace KWCMS\modules\Files\File;


use kalanis\kw_files\FilesException;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_forms\Interfaces\IMultiValue;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_langs\Lang;


/**
 * Class Copy
 * @package KWCMS\modules\Files\File
 * Copy content
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
        try {
            $this->userDir->setUserPath($this->user->getDir());
            $this->userDir->process();

            $this->tree->startFromPath($this->userDir->getHomeDir());
            $this->tree->canRecursive(true);
            $this->tree->setFilterCallback([$this, 'filterDirs']);
            $this->tree->process();
            $targetTree = $this->tree->getTree();
            $this->tree->startFromPath($this->userDir->getHomeDir() . $this->getWhereDir());
            $this->tree->canRecursive(false);
            $this->tree->setFilterCallback([$this, 'filterFiles']);
            $this->tree->process();
            $sourceTree = $this->tree->getTree();

            $this->fileForm->composeCopyFile($sourceTree, $targetTree);
            $this->fileForm->setInputs(new InputVarsAdapter($this->inputs));
            if ($this->fileForm->process()) {
                $entries = $this->fileForm->getControl('sourceName[]');
                if (!$entries instanceof IMultiValue) {
                    throw new FilesException(Lang::get('files.error.must_contain_files'));
                }
                $actionLib = $this->getLibAction();
                foreach ($entries->getValues() as $item) {
                    $this->processed[$item] = $actionLib->copyFile(
                        $item,
                        $this->fileForm->getControl('targetPath')->getValue()
                    );
                }
                $this->tree->process();
                $sourceTree = $this->tree->getTree();
                $this->fileForm->composeCopyFile($sourceTree, $targetTree); // again, changes in tree
                $this->fileForm->setInputs(new InputVarsAdapter($this->inputs));
                $this->fileForm->setSentValues();
            }
        } catch (FilesException | FormsException $ex) {
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
