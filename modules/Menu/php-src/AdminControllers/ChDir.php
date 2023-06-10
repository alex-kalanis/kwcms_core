<?php

namespace KWCMS\modules\Menu\AdminControllers;


use kalanis\kw_langs\Lang;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Output;
use kalanis\kw_paths\Stored;
use kalanis\kw_routed_paths\StoreRouted;
use KWCMS\modules\Menu\Templates;


/**
 * Class ChDir
 * @package KWCMS\modules\Menu\AdminControllers
 * Change directory - by texts
 */
class ChDir extends \KWCMS\modules\Admin\AdminControllers\ChDir implements IModuleTitle
{
    use Templates\TModuleTemplate;

    public function __construct()
    {
        parent::__construct();
        $this->initTModuleTemplate(Stored::getPath(), StoreRouted::getPath());
    }

    protected function htmlContent(string $content): Output\AOutput
    {
        $out = new Output\Html();
        return $out->setContent($this->outModuleTemplate($content));
    }

    public function getTitle(): string
    {
        return Lang::get('menu.page') . ' - ' . Lang::get('dashboard.dir_select');
    }
}
