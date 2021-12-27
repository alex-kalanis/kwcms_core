<?php

namespace KWCMS\modules\Menu;


use kalanis\kw_confs\Config;
use kalanis\kw_langs\Lang;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Output;


/**
 * Class ChDir
 * @package KWCMS\modules\Menu
 * Change directory - by texts
 */
class ChDir extends \KWCMS\modules\Admin\ChDir implements IModuleTitle
{
    use Templates\TModuleTemplate;

    public function __construct()
    {
        parent::__construct();
        $this->initTModuleTemplate(Config::getPath());
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
