<?php

namespace KWCMS\modules\Files\File;


use kalanis\kw_auth\Interfaces\IAccessClasses;
use kalanis\kw_confs\Config;
use kalanis\kw_extras\UserDir;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_forms\Interfaces\IMultiValue;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_langs\Lang;
use kalanis\kw_modules\AAuthModule;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Output;
use kalanis\kw_notify\Notification;
use kalanis\kw_tree\Tree;
use kalanis\kw_tree\TWhereDir;
use KWCMS\modules\Admin\Shared;
use KWCMS\modules\Files\FilesException;
use KWCMS\modules\Files\Lib;


/**
 * Class Move
 * @package KWCMS\modules\Files\File
 * Move content
 */
class Move extends AAuthModule implements IModuleTitle
{
    use Lib\TLibAction;
    use Lib\TModuleTemplate;
    use Lib\TParams;
    use TWhereDir;

    /** @var UserDir|null */
    protected $userDir = null;
    /** @var Tree|null */
    protected $tree = null;
    /** @var Lib\FileForm|null */
    protected $fileForm = null;
    /** @var bool[] */
    protected $processed = [];

    public function __construct()
    {
        $this->initTModuleTemplate();
        $this->tree = new Tree(Config::getPath());
        $this->userDir = new UserDir(Config::getPath());
        $this->fileForm = new Lib\FileForm('moveFileForm');
    }

    public function allowedAccessClasses(): array
    {
        return [IAccessClasses::CLASS_MAINTAINER, IAccessClasses::CLASS_ADMIN, IAccessClasses::CLASS_USER, ];
    }

    public function run(): void
    {
        $this->initWhereDir(new SessionAdapter(), $this->inputs);
        try {
            $this->userDir->setUserPath($this->user->getDir());
            $this->userDir->process();

            $this->tree->startFromPath($this->userDir->getRealDir());
            $this->tree->canRecursive(true);
            $this->tree->setFilterCallback([$this, 'filterDirs']);
            $this->tree->process();
            $targetTree = $this->tree->getTree();
            $this->tree->startFromPath($this->userDir->getRealDir() . $this->getWhereDir());
            $this->tree->canRecursive(false);
            $this->tree->setFilterCallback([$this, 'filterFiles']);
            $this->tree->process();
            $sourceTree = $this->tree->getTree();

            $this->fileForm->composeMoveFile($sourceTree, $targetTree);
            $this->fileForm->setInputs(new InputVarsAdapter($this->inputs));
            if ($this->fileForm->process()) {
                $entries = $this->fileForm->getControl('sourceName[]');
                if (!$entries instanceof IMultiValue) {
                    throw new FilesException(Lang::get('files.error.must_contain_files'));
                }
                $actionLib = $this->getLibFileAction();
                foreach ($entries->getValues() as $item) {
                    $this->processed[$item] = $actionLib->moveFile(
                        $item,
                        $this->fileForm->getControl('targetPath')->getValue()
                    );
                }
                $this->tree->process();
                $sourceTree = $this->tree->getTree();
                $this->fileForm->composeMoveFile($sourceTree, $targetTree); // again, change in tree
                $this->fileForm->setInputs(new InputVarsAdapter($this->inputs));
                $this->fileForm->setSentValues();
            }
        } catch (FilesException | FormsException $ex) {
            $this->error = $ex;
        }
    }

    protected function getUserDir(): string
    {
        return $this->user->getDir();
    }

    public function result(): Output\AOutput
    {
        return $this->isJson()
            ? $this->outJson()
            : $this->outHtml();
    }

    public function outHtml(): Output\AOutput
    {
        $out = new Shared\FillHtml($this->user);
        $page = new Lib\OperationTemplate();
        if ($this->error) {
            Notification::addError($this->error->getMessage());
        }
        try {
            foreach ($this->processed as $name => $status) {
                if ($status) {
                    Notification::addSuccess(Lang::get('files.uploaded', $name));
                } else {
                    Notification::addError(Lang::get('files.uploaded', $name));
                }
            }
            return $out->setContent($this->outModuleTemplate($page->setData(
                $this->fileForm,
                Lang::get('files.file.move')
            )->render()));
        } catch (FilesException | FormsException $ex) {
            $this->error = $ex;
        }
        return $out->setContent($this->outModuleTemplate($this->error->getMessage()));
    }

    public function outJson(): Output\AOutput
    {
        if ($this->error) {
            $out = new Output\JsonError();
            return $out->setContent($this->error->getCode(), $this->error->getMessage());
        } else {
            $out = new Output\Json();
            $out->setContent([
                'form_result' => $this->processed,
                'form_errors' => $this->fileForm->renderErrorsArray(),
            ]);
            return $out;
        }
    }

    public function getTitle(): string
    {
        return Lang::get('files.page') . ' - ' . Lang::get('files.file.move.short');
    }
}
