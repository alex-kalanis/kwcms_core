<?php

namespace KWCMS\modules\Files\AdminControllers\Dir;


use kalanis\kw_files\FilesException;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_forms\Interfaces\IMultiValue;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_langs\Lang;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;
use KWCMS\modules\Files\Lib;


/**
 * Class Delete
 * @package KWCMS\modules\Files\AdminControllers\Dir
 * Delete content
 */
class Delete extends ADir
{
    protected function getFormAlias(): string
    {
        return 'deleteDirForm';
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

            $this->dirForm->composeDeleteDir($this->tree->getRoot());
            $this->dirForm->setInputs(new InputVarsAdapter($this->inputs));

            if ($this->dirForm->process()) {
                $entries = $this->dirForm->getControl('sourceName[]');
                if (!$entries instanceof IMultiValue) {
                    throw new FilesException(Lang::get('files.error.must_contain_files'));
                }
                if ('yes' != $this->dirForm->getControl('targetPath')->getValue()) {
                    return;
                }
                $this->processor->setUserPath($userPath)->setWorkPath($workPath);
                foreach ($entries->getValues() as $item) {
                    $this->processed[$item] = $this->processor->deleteDir($item);
                }
                $this->tree->process();
                $this->dirForm->composeDeleteDir($this->tree->getRoot()); // again, changes in tree
            }
        } catch (FilesException | FormsException | PathsException $ex) {
            $this->error = $ex;
        }
    }

    protected function getFormTitleLangKey(): string
    {
        return 'files.dir.delete';
    }

    protected function getSuccessLangKey(): string
    {
        return 'files.dir.deleted';
    }

    protected function getFailureLangKey(): string
    {
        return 'files.dir.not_deleted';
    }

    protected function getTitleLangKey(): string
    {
        return 'files.dir.delete.short';
    }
}
