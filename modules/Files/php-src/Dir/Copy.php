<?php

namespace KWCMS\modules\Files\Dir;


use kalanis\kw_files\FilesException;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_forms\Interfaces\IMultiValue;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_langs\Lang;


/**
 * Class Copy
 * @package KWCMS\modules\Files\Dir
 * Copy content
 */
class Copy extends ADir
{
    protected function getFormAlias(): string
    {
        return 'copyDirForm';
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
            $this->tree->setFilterCallback([$this, 'filterDirs']);
            $this->tree->process();
            $sourceTree = $this->tree->getTree();

            $this->dirForm->composeCopyDir($sourceTree, $targetTree);
            $this->dirForm->setInputs(new InputVarsAdapter($this->inputs));
            if ($this->dirForm->process()) {
                $entries = $this->dirForm->getControl('sourceName[]');
                if (!$entries instanceof IMultiValue) {
                    throw new FilesException(Lang::get('files.error.must_contain_files'));
                }
                $actionLib = $this->getLibDirAction();
                foreach ($entries->getValues() as $item) {
                    $this->processed[$item] = $actionLib->copyDir(
                        $item,
                        $this->dirForm->getControl('targetPath')->getValue()
                    );
                }
                $this->tree->process();
                $sourceTree = $this->tree->getTree();
                $this->dirForm->composeCopyDir($sourceTree, $targetTree); // again, changes in tree
                $this->dirForm->setInputs(new InputVarsAdapter($this->inputs));
                $this->dirForm->setSentValues();
            }
        } catch (FilesException | FormsException $ex) {
            $this->error = $ex;
        }
    }

    protected function getFormTitleLangKey(): string
    {
        return 'files.dir.copy';
    }

    protected function getSuccessLangKey(): string
    {
        return 'files.dir.copied';
    }

    protected function getFailureLangKey(): string
    {
        return 'files.dir.not_copied';
    }

    protected function getTitleLangKey(): string
    {
        return 'files.dir.copy.short';
    }
}
