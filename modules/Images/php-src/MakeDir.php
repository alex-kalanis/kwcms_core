<?php

namespace KWCMS\modules\Images;


use kalanis\kw_auth\Interfaces\IAccessClasses;
use kalanis\kw_confs\Config;
use kalanis\kw_extras\UserDir;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_images\ImagesException;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_langs\Lang;
use kalanis\kw_modules\AAuthModule;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Output;
use kalanis\kw_notify\Notification;
use kalanis\kw_tree\Tree;
use kalanis\kw_tree\TWhereDir;
use KWCMS\modules\Admin\Shared;
use SplFileInfo;


/**
 * Class MakeDir
 * @package KWCMS\modules\Images
 * Directory creation in another path
 */
class MakeDir extends AAuthModule implements IModuleTitle
{
    use Lib\TLibAction;
    use Templates\TModuleTemplate;
    use TWhereDir;

    /** @var Forms\DirNewForm|null */
    protected $createForm = null;
    /** @var UserDir|null */
    protected $userDir = null;
    /** @var Tree|null */
    protected $tree = null;
    /** @var bool */
    protected $processed = false;

    public function __construct()
    {
        $this->initTModuleTemplate();
        $this->createForm = new Forms\DirNewForm('dirNewForm');
        $this->tree = new Tree(Config::getPath());
        $this->userDir = new UserDir(Config::getPath());
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
            $this->tree->canRecursive(true);
            $this->tree->startFromPath($this->userDir->getHomeDir());
            $this->tree->setFilterCallback([$this, 'filterDirs']);
            $this->tree->process();
            $this->createForm->composeForm($this->tree->getTree(),'#');
            $this->createForm->setInputs(new InputVarsAdapter($this->inputs));
            if ($this->createForm->process()) {
                $libAction = $this->getLibDirAction();
                $this->processed = $libAction->createDir(
                    strval($this->createForm->getControl('where')->getValue()),
                    strval($this->createForm->getControl('name')->getValue())
                );
                if (!empty($this->createForm->getControl('into')->getValue())) {
                    $this->updateWhereDir(
                        strval($this->createForm->getControl('where')->getValue())
                        . DIRECTORY_SEPARATOR
                        . strval($this->createForm->getControl('name')->getValue())
                    );
                }
            }
        } catch (ImagesException | FormsException $ex) {
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
        $page = new Templates\DirNewTemplate();
        if ($this->error) {
            Notification::addError($this->error->getMessage());
        }
        try {
            if ($this->processed) {
                Notification::addSuccess(Lang::get('images.dir_created'));
            }
            return $out->setContent($this->outModuleTemplate($page->setData($this->createForm)->render()));
        } catch (FormsException $ex) {
            $this->error = $ex;
        }
        return $out->setContent($this->outModuleTemplate($this->error->getMessage() . nl2br($this->error->getTraceAsString())));
    }

    public function outJson(): Output\AOutput
    {
        if ($this->error) {
            $out = new Output\JsonError();
            return $out->setContent($this->error->getCode(), $this->error->getMessage());
        } else {
            $out = new Output\Json();
            $out->setContent([
                'form_result' => intval($this->processed),
                'form_errors' => $this->createForm->renderErrorsArray(),
            ]);
            return $out;
        }
    }

    public function getTitle(): string
    {
        return Lang::get('images.page') . ' - ' . Lang::get('images.dir_create.short');
    }

    public function filterDirs(SplFileInfo $info): bool
    {
        return $info->isDir();
    }
}
