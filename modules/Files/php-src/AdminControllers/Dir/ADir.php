<?php

namespace KWCMS\modules\Files\AdminControllers\Dir;


use kalanis\kw_auth\Interfaces\IAccessClasses;
use kalanis\kw_files\Access;
use kalanis\kw_files\FilesException;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_forms\Exceptions\RenderException;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_modules\AAuthModule;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Output;
use kalanis\kw_notify\Notification;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stored;
use kalanis\kw_tree\DataSources;
use kalanis\kw_tree\Interfaces\ITree;
use kalanis\kw_tree\Traits\TFilesDirs;
use kalanis\kw_tree_controls\TWhereDir;
use kalanis\kw_user_paths\UserDir;
use KWCMS\modules\Core\Libs\FilesTranslations;
use KWCMS\modules\Files\Lib;


/**
 * Class ADir
 * @package KWCMS\modules\Files\AdminControllers\Dir
 * Abstract to actions with directories
 */
abstract class ADir extends AAuthModule implements IModuleTitle
{
    use Lib\TModuleTemplate;
    use TWhereDir;
    use TFilesDirs;

    /** @var UserDir */
    protected $userDir = null;
    /** @var ITree */
    protected $tree = null;
    /** @var Lib\Processor */
    protected $processor = null;
    /** @var Lib\DirForm|null */
    protected $dirForm = null;
    /** @var bool[] */
    protected $processed = [];

    /**
     * @throws FilesException
     * @throws LangException
     * @throws PathsException
     */
    public function __construct()
    {
        $this->initTModuleTemplate();
        $files = (new Access\Factory(new FilesTranslations()))->getClass(
            Stored::getPath()->getDocumentRoot() . Stored::getPath()->getPathToSystemRoot()
        );
        $this->tree = new DataSources\Files($files);
        $this->processor = new Lib\Processor($files);
        $this->userDir = new UserDir(new Lib\Translations());
        $this->dirForm = new Lib\DirForm($this->getFormAlias());
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

    /**
     * @throws RenderException
     * @return Output\AOutput
     */
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
                $this->dirForm,
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

    /**
     * @throws RenderException
     * @return Output\AOutput
     */
    public function outJson(): Output\AOutput
    {
        if ($this->error) {
            $out = new Output\JsonError();
            return $out->setContent($this->error->getCode(), $this->error->getMessage());
        } else {
            $out = new Output\Json();
            $out->setContent([
                'form_result' => $this->processed,
                'form_errors' => $this->dirForm->renderErrorsArray(),
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
