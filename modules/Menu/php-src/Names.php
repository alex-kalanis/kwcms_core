<?php

namespace KWCMS\modules\Menu;


use kalanis\kw_auth\Interfaces\IAccessClasses;
use kalanis\kw_confs\Config;
use kalanis\kw_connect\core\ConnectException;
use kalanis\kw_langs\Lang;
use kalanis\kw_menu\MenuException;
use kalanis\kw_modules\AAuthModule;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Output;
use kalanis\kw_styles\Styles;
use kalanis\kw_table\core\TableException;
use KWCMS\modules\Admin\Shared;


/**
 * Class Names
 * @package KWCMS\modules\Menu
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

    public function __construct()
    {
        $this->initTModuleTemplate(Config::getPath());
        $this->initTMenu(Config::getPath());
    }

    public function allowedAccessClasses(): array
    {
        return [IAccessClasses::CLASS_MAINTAINER, IAccessClasses::CLASS_ADMIN, IAccessClasses::CLASS_USER, ];
    }

    public function run(): void
    {
        try {
            $this->runTMenu($this->inputs, $this->user->getDir());
        } catch (MenuException $ex) {
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
        $table = new Lib\ItemTable($this->links);
        if (!$this->error) {
            try {
                Styles::want('Menu', 'menu.css');
                return $out->setContent($this->outModuleTemplate($table->prepareHtml($this->libMenu->getData())));
            } catch (ConnectException | TableException $ex) {
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
            return $out->setContent($table->prepareJson($this->libMenu->load()->getData()));
        } catch (ConnectException | TableException | MenuException $ex) {
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
