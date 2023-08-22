<?php

namespace KWCMS\modules\Menu\AdminControllers;


use kalanis\kw_auth_sources\Interfaces\IWorkClasses;
use kalanis\kw_confs\ConfException;
use kalanis\kw_connect\core\ConnectException;
use kalanis\kw_files\FilesException;
use kalanis\kw_forms\Exceptions\RenderException;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_menu\MenuException;
use kalanis\kw_modules\AAuthModule;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Output;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stored;
use kalanis\kw_routed_paths\StoreRouted;
use kalanis\kw_semaphore\SemaphoreException;
use kalanis\kw_styles\Styles;
use kalanis\kw_table\core\TableException;
use KWCMS\modules\Menu\Lib;
use KWCMS\modules\Menu\Templates;


/**
 * Class Names
 * @package KWCMS\modules\Menu\AdminControllers
 * Site's Menu - Names of files
 */
class Names extends AAuthModule implements IModuleTitle
{
    use Lib\TMenu;
    use Templates\TModuleTemplate;

    /** @var MenuException|null */
    protected $error = null;
    /** @var bool */
    protected $isProcessed = false;

    /**
     * @throws ConfException
     * @throws FilesException
     * @throws LangException
     * @throws MenuException
     * @throws PathsException
     */
    public function __construct()
    {
        $this->initTModuleTemplate(Stored::getPath(), StoreRouted::getPath());
        $this->initTMenu(Stored::getPath());
    }

    public function allowedAccessClasses(): array
    {
        return [IWorkClasses::CLASS_MAINTAINER, IWorkClasses::CLASS_ADMIN, IWorkClasses::CLASS_USER, ];
    }

    public function run(): void
    {
        try {
            $this->runTMenu($this->inputs, $this->user->getDir());
        } catch (MenuException | PathsException | SemaphoreException $ex) {
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
        $table = new Lib\ItemTable($this->links);
        if (!$this->error) {
            try {
                Styles::want('Menu', 'menu.css');
                return $out->setContent($this->outModuleTemplate($table->prepareHtml($this->libMenu->getMeta())));
            } catch (ConnectException | TableException | RenderException $ex) {
                $this->error = $ex;
            }
        }

        if ($this->error) {
            return $out->setContent($this->outModuleTemplate($this->error->getMessage() . nl2br($this->error->getTraceAsString())));
        } else {
            return $out->setContent($this->outModuleTemplate(Lang::get('menu.error.cannot_read')));
        }
    }

    public function outJson(): Output\AOutput
    {
        $out = new Output\Json();
        $table = new Lib\ItemTable($this->links);
        try {
            return $out->setContent($table->prepareJson($this->libMenu->load()->getMeta()));
        } catch (ConnectException | TableException | MenuException | PathsException $ex) {
            $this->error = $ex;
        }

        if ($this->error) {
            $out = new Output\JsonError();
            return $out->setContent($this->error->getCode(), $this->error->getMessage());
        } else {
            return $out->setContent(Lang::get('menu.error.cannot_read'));
        }
    }

    public function getTitle(): string
    {
        return Lang::get('menu.page') . ' - ' . Lang::get('menu.tree.current_dir.file_data');
    }
}
