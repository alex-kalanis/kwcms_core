<?php

namespace KWCMS\modules\Menu;


use kalanis\kw_auth\Interfaces\IAccessClasses;
use kalanis\kw_confs\Config;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_langs\Lang;
use kalanis\kw_menu\MenuException;
use kalanis\kw_modules\AAuthModule;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Output;
use kalanis\kw_scripts\Scripts;
use kalanis\kw_semaphore\SemaphoreException;
use kalanis\kw_styles\Styles;


/**
 * Class Dashboard
 * @package KWCMS\modules\Menu
 * Site's Menu - list available actions
 */
class Dashboard extends AAuthModule implements IModuleTitle
{
    use Lib\TMenu;
    use Templates\TModuleTemplate;

    /** @var Forms\EditPropsForm|null */
    protected $editPropsForm = null;
    /** @var bool */
    protected $isProcessed = false;

    public function __construct()
    {
        $this->initTModuleTemplate(Config::getPath());
        $this->initTMenu(Config::getPath());
        $this->editPropsForm = new Forms\EditPropsForm('editPropsForm');
    }

    public function allowedAccessClasses(): array
    {
        return [IAccessClasses::CLASS_MAINTAINER, IAccessClasses::CLASS_ADMIN, IAccessClasses::CLASS_USER, ];
    }

    public function run(): void
    {
        try {
            $this->runTMenu($this->inputs, $this->user->getDir());
            $item = $this->libMenu->getMeta()->getMenu();
            if (empty($item->getFile())) {
                $item->setData($this->getWhereDir(), $item->getName(), $item->getTitle(), $item->getDisplayCount());
            }
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
                $this->libMenu->getMeta()->updateInfo(
                    (string)$this->editPropsForm->getControl('menuName')->getValue(),
                    (string)$this->editPropsForm->getControl('menuDesc')->getValue(),
                    (int)$this->editPropsForm->getControl('menuCount')->getValue()
                );
                $this->libMenu->getMeta()->save();
                $this->libSemaphore->want();
                $this->isProcessed = true;
            }
        } catch (FormsException | MenuException | SemaphoreException $ex) {
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
        $out = new Output\Html();
        if (!empty($this->error)) {
            return $out->setContent($this->outModuleTemplate($this->error->getMessage() . nl2br($this->error->getTraceAsString())));
        }
        Styles::want('Menu', 'menu.css');
        Scripts::want('Menu', 'counter.js');
        $page = new Templates\DashboardTemplate();
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
