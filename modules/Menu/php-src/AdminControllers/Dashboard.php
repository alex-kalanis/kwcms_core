<?php

namespace KWCMS\modules\Menu\AdminControllers;


use kalanis\kw_accounts\Interfaces\IProcessClasses;
use kalanis\kw_confs\ConfException;
use kalanis\kw_files\FilesException;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_forms\Exceptions\RenderException;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_menu\MenuException;
use kalanis\kw_modules\Output;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;
use kalanis\kw_routed_paths\StoreRouted;
use kalanis\kw_scripts\Scripts;
use kalanis\kw_semaphore\SemaphoreException;
use kalanis\kw_styles\Styles;
use KWCMS\modules\Core\Interfaces\Modules\IHasTitle;
use KWCMS\modules\Core\Libs\AAuthModule;
use KWCMS\modules\Menu\Forms;
use KWCMS\modules\Menu\Lib;
use KWCMS\modules\Menu\Templates;


/**
 * Class Dashboard
 * @package KWCMS\modules\Menu\AdminControllers
 * Site's Menu - list available actions
 */
class Dashboard extends AAuthModule implements IHasTitle
{
    use Lib\TMenu;
    use Templates\TModuleTemplate;

    /** @var Forms\EditPropsForm */
    protected $editPropsForm = null;
    /** @var bool */
    protected $isProcessed = false;

    /**
     * @param mixed ...$constructParams
     * @throws ConfException
     * @throws FilesException
     * @throws LangException
     * @throws MenuException
     * @throws PathsException
     */
    public function __construct(...$constructParams)
    {
        $this->initTModuleTemplate(StoreRouted::getPath());
        $this->initTMenu($constructParams);
        $this->editPropsForm = new Forms\EditPropsForm('editPropsForm');
    }

    public function allowedAccessClasses(): array
    {
        return [IProcessClasses::CLASS_MAINTAINER, IProcessClasses::CLASS_ADMIN, IProcessClasses::CLASS_USER, ];
    }

    public function run(): void
    {
        try {
            $this->runTMenu($this->inputs, $this->user->getDir());
            $item = $this->libMenu->getMeta()->getMenu();
            if (empty($item->getFile())) {
                $item->setData($this->getWhereDir(), $item->getName(), $item->getTitle(), $item->getDisplayCount());
            }
        } catch (MenuException | PathsException | SemaphoreException $ex) {
            $item = new \kalanis\kw_menu\Menu\Menu();
            $item->setData(
                Stuff::filename($this->getWhereDir()),
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
                    strval($this->editPropsForm->getControl('menuName')->getValue()),
                    strval($this->editPropsForm->getControl('menuDesc')->getValue()),
                    intval($this->editPropsForm->getControl('menuCount')->getValue())
                );
                $this->libMenu->getMeta()->save();
                $this->libSemaphore->want();
                $this->isProcessed = true;
            }
        } catch (FormsException | MenuException | SemaphoreException $ex) {
            $this->error = $ex;
        }
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

    /**
     * @throws RenderException
     * @return Output\AOutput
     */
    public function outHtml(): Output\AOutput
    {
        $out = new Output\Html();
        if (!empty($this->error)) {
            return $out->setContent($this->outModuleTemplate($this->error->getMessage() . nl2br($this->error->getTraceAsString())));
        }
        Styles::want('menu', 'menu.css');
        Scripts::want('menu', 'counter.js');
        $page = new Templates\DashboardTemplate();
        return $out->setContent($this->outModuleTemplate($page->setData($this->editPropsForm)->render()));
    }

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
