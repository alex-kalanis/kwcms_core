<?php

namespace KWCMS\modules\Menu;


use kalanis\kw_auth\Interfaces\IAccessClasses;
use kalanis\kw_confs\Config;
use kalanis\kw_extras\UserDir;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_langs\Lang;
use kalanis\kw_menu\MenuException;
use kalanis\kw_modules\AAuthModule;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Output;
use kalanis\kw_paths\Stuff;
use kalanis\kw_scripts\Scripts;
use kalanis\kw_styles\Styles;
use kalanis\kw_tree\TWhereDir;
use KWCMS\modules\Admin\Shared;


/**
 * Class Dashboard
 * @package KWCMS\modules\Menu
 * Site's Menu - list available actions
 */
class Dashboard extends AAuthModule implements IModuleTitle
{
    use Lib\TModuleTemplate;
    use TWhereDir;

    /** @var UserDir|null */
    protected $userDir = null;
    /** @var Lib\EditPropsForm|null */
    protected $editPropsForm = null;
    /** @var bool */
    protected $isProcessed = false;
    protected $libMenu = null;

    public function __construct()
    {
        $this->initTModuleTemplate();
        $this->userDir = new UserDir(Config::getPath());
        $this->editPropsForm = new Lib\EditPropsForm('editPropsForm');
        $this->libMenu = new \kalanis\kw_menu\DataProcessor();
        Config::load('Menu');
    }

    public function allowedAccessClasses(): array
    {
        return [IAccessClasses::CLASS_MAINTAINER, IAccessClasses::CLASS_ADMIN, IAccessClasses::CLASS_USER, ];
    }

    public function run(): void
    {
        $this->initWhereDir(new SessionAdapter(), $this->inputs);
        $this->userDir->setUserPath($this->user->getDir());
        $this->userDir->process();

        $this->libMenu->setPath(
            Stuff::removeEndingSlash($this->userDir->getWebRootDir()) . DIRECTORY_SEPARATOR
            . Stuff::removeEndingSlash($this->userDir->getRealDir()) . DIRECTORY_SEPARATOR
            . Stuff::removeEndingSlash($this->getWhereDir()) . DIRECTORY_SEPARATOR
            . Config::get('Menu', 'meta')
        );
        try {
            $item = $this->libMenu->getMenu();
        } catch (MenuException $ex) {
            $item = new \kalanis\kw_menu\Menu\Menu();
            $item->setData(
                $this->getWhereDir(),
                $this->getWhereDir(),
                $this->getWhereDir(),
                0
            );
        }
        try {
            $this->editPropsForm->composeForm($item);
            $this->editPropsForm->setInputs(new InputVarsAdapter($this->inputs));
            if ($this->editPropsForm->process()) {
                $this->libMenu->updateInfo(
                    (string)$this->editPropsForm->getControl('menuName')->getValue(),
                    (string)$this->editPropsForm->getControl('menuDesc')->getValue(),
                    (int)$this->editPropsForm->getControl('menuCount')->getValue()
                );
                $this->isProcessed = true;
            }
        } catch (FormsException $ex) {
            $this->error = $ex;
        }
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
        if (!empty($this->error)) {
            return $out->setContent($this->outModuleTemplate($this->error->getMessage()));
        }
        Styles::want('Menu', 'menu.css');
        Scripts::want('Menu', 'counter.js');
        $page = new Lib\DashboardTemplate();
        return $out->setContent($this->outModuleTemplate($page->setData($this->editPropsForm)->render()));
    }

    public function outJson(): Output\AOutput
    {
        if ($this->error) {
            $out = new Output\JsonError();
            return $out->setContent($this->error->getCode(), $this->error->getMessage());
        } else {
            $out = new Output\Json();
            $out->setContent([
                'form_result' => intval($this->isProcessed),
                'form_errors' => $this->editPropsForm->renderErrorsArray(),
            ]);
            return $out;
        }
    }

    public function getTitle(): string
    {
        return Lang::get('menu.page');
    }
}
