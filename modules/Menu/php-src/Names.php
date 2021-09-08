<?php

namespace KWCMS\modules\Menu;


use kalanis\kw_auth\Interfaces\IAccessClasses;
use kalanis\kw_confs\Config;
use kalanis\kw_extras\UserDir;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_langs\Lang;
use kalanis\kw_mapper\MapperException;
use kalanis\kw_menu as menu;
use kalanis\kw_menu\MenuException;
use kalanis\kw_modules\AAuthModule;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Output;
use kalanis\kw_paths\Stuff;
use kalanis\kw_table\TableException;
use kalanis\kw_tree\TWhereDir;
use KWCMS\modules\Admin\Shared;


/**
 * Class Names
 * @package KWCMS\modules\Menu
 * Site's Menu - Names of files
 *
 * @todo: Tahle buzna je TABULKA!!! Takže edit dostane extra stránku, kde se bude upravovat podle jména souboru.
 * @todo: Umístění je tabulka taky, ale až moc specifická. Takže bude potřeba jí udělat specificky.
 */
class Names extends AAuthModule implements IModuleTitle
{
    use Lib\TModuleTemplate;
    use TWhereDir;

    /** @var MenuException|null */
    protected $error = null;
    /** @var UserDir|null */
    protected $userDir = null;
    /** @var bool */
    protected $isProcessed = false;
    /** @var menu\MoreFiles|null - NEW DATASOURCE */
    protected $libMenu = null;

    public function __construct()
    {
        Config::load('Menu');
        $this->initTModuleTemplate();
        $this->userDir = new UserDir(Config::getPath());
        $this->libMenu = new menu\MoreFiles(
            new menu\DataSource\Volume($this->userDir->getWebRootDir()),
            Config::get('Menu', 'meta')
        );
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
        try {
            $this->libMenu->setPath(
                Stuff::removeEndingSlash($this->userDir->getRealDir()) . DIRECTORY_SEPARATOR
                . Stuff::removeEndingSlash($this->getWhereDir()) . DIRECTORY_SEPARATOR
            );
            $this->libMenu->load();
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
                return $out->setContent($this->outModuleTemplate($table->prepareHtml($this->libMenu->load()->getData())));
            } catch (MapperException | TableException | MenuException $ex) {
                $this->error = $ex;
            }
        }

        if ($this->error) {
            return $out->setContent($this->outModuleTemplate($this->error->getMessage()));
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
        } catch (MapperException | TableException | MenuException $ex) {
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
