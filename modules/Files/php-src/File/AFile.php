<?php

namespace KWCMS\modules\Files\File;


use kalanis\kw_auth\Interfaces\IAccessClasses;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_langs\Lang;
use kalanis\kw_modules\AAuthModule;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Output;
use kalanis\kw_notify\Notification;
use kalanis\kw_paths\Extras\UserDir;
use kalanis\kw_paths\Stored;
use kalanis\kw_tree\Tree;
use kalanis\kw_tree\TWhereDir;
use KWCMS\modules\Files\FilesException;
use KWCMS\modules\Files\Lib;


/**
 * Class AFile
 * @package KWCMS\modules\Files\File
 * Abstract to actions with files
 */
abstract class AFile extends AAuthModule implements IModuleTitle
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
        $this->tree = new Tree(Stored::getPath());
        $this->userDir = new UserDir(Stored::getPath());
        $this->fileForm = new Lib\FileForm($this->getFormAlias());
    }

    abstract protected function getFormAlias(): string;

    public function allowedAccessClasses(): array
    {
        return [IAccessClasses::CLASS_MAINTAINER, IAccessClasses::CLASS_ADMIN, IAccessClasses::CLASS_USER, ];
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
        $out = new Output\Html();
        $page = new Lib\OperationTemplate();
        if ($this->error) {
            Notification::addError($this->error->getMessage());
        }
        try {
            foreach ($this->processed as $name => $status) {
                if ($status) {
                    Notification::addSuccess(Lang::get($this->getSuccessLangKey(), $name));
                } else {
                    Notification::addError(Lang::get($this->getFailureLangKey(), $name));
                }
            }
            return $out->setContent($this->outModuleTemplate($page->setData(
                $this->fileForm,
                Lang::get($this->getFormTitleLangKey())
            )->render()));
        } catch (FilesException | FormsException $ex) {
            $this->error = $ex;
        }
        return $out->setContent($this->outModuleTemplate($this->error->getMessage() . nl2br($this->error->getTraceAsString())));
    }

    abstract protected function getFormTitleLangKey(): string;

    abstract protected function getSuccessLangKey(): string;

    abstract protected function getFailureLangKey(): string;

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
        return Lang::get('files.page') . ' - ' . Lang::get($this->getTitleLangKey());
    }

    abstract protected function getTitleLangKey(): string;
}
