<?php

namespace KWCMS\modules\Images;


use kalanis\kw_confs\Config;
use kalanis\kw_langs\Lang;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Output;


/**
 * Class ChDir
 * @package KWCMS\modules\Images
 * Change directory - by Images
 */
class ChDir extends \KWCMS\modules\Admin\ChDir implements IModuleTitle
{
    use Templates\TModuleTemplate;

    public function __construct()
    {
        parent::__construct();
        $this->initTModuleTemplate();
        Config::load('Images');
    }

    protected function htmlContent(string $content): Output\AOutput
    {
        $out = new Output\Html();
        return $out->setContent($this->outModuleTemplate($content));
    }

    public function getTitle(): string
    {
        return Lang::get('images.page') . ' - ' . Lang::get('dashboard.dir_select');
    }
}
