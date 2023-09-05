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
 * Class Positions
 * @package KWCMS\modules\Menu\AdminControllers
 * Site's Menu - update positions of each item
 */
class Positions extends AAuthModule implements IHasTitle
{
    use Lib\TMenu;
    use Templates\TModuleTemplate;

    /** @var Forms\EditPosForm|null */
    protected $editPosForm = null;
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
        $this->editPosForm = new Forms\EditPosForm('editPosForm');
    }

    public function allowedAccessClasses(): array
    {
        return [IProcessClasses::CLASS_MAINTAINER, IProcessClasses::CLASS_ADMIN, IProcessClasses::CLASS_USER, ];
    }

    public function run(): void
    {
        try {
            $this->runTMenu($this->inputs, $this->user->getDir());
            $this->editPosForm->composeForm($this->libMenu->getMeta()->getWorking(), $this->libMenu->getMeta()->getMenu()->getDisplayCount());
            $this->editPosForm->setInputs(new InputVarsAdapter($this->inputs));
            if ($this->editPosForm->process()) {
                $this->libMenu->getMeta()->rearrangePositions($this->editPosForm->getPositions());
                $this->libMenu->getMeta()->save();
                $this->libSemaphore->want();
                // AGAIN! - re-create form
                $this->editPosForm = new Forms\EditPosForm('editPosForm');
                $this->editPosForm->composeForm($this->libMenu->getMeta()->getWorking(), $this->libMenu->getMeta()->getMenu()->getDisplayCount());
                $this->isProcessed = true;
            }
        } catch (FormsException | MenuException | SemaphoreException | PathsException $ex) {
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
        Styles::want('Menu', 'menu.css');
        Scripts::want('Menu', 'positions.js');
        $page = new Templates\PositionsTemplate();
        $item = new Templates\PositionItemTemplate();
        $entries = [];
        foreach ($this->editPosForm->getInputs() as $input) {
            $entries[] = $item->reset()->setData($input)->render();
        }
        return $out->setContent($this->outModuleTemplate($page->setData($this->editPosForm, implode('', $entries))->render()));
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
                'form_errors' => $this->editPosForm->renderErrorsArray(),
            ]);
            return $out;
        }
    }

    public function getTitle(): string
    {
        return Lang::get('menu.page');
    }
}
