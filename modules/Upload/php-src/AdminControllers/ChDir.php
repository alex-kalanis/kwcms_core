<?php

namespace KWCMS\modules\Upload\AdminControllers;


use kalanis\kw_langs\Lang;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Output;
use KWCMS\modules\Upload\Lib;


/**
 * Class ChDir
 * @package KWCMS\modules\Upload\AdminControllers
 * Change directory - by Upload
 */
class ChDir extends \KWCMS\modules\Admin\AdminControllers\ChDir implements IModuleTitle
{
    use Lib\TModuleTemplate;

    public function __construct()
    {
        parent::__construct();
        $this->initTModuleTemplate();
    }

    protected function htmlContent(string $content): Output\AOutput
    {
        $out = new Output\Html();
        return $out->setContent($this->outModuleTemplate($content));
    }

    public function getTitle(): string
    {
        return Lang::get('upload.page') . ' - ' . Lang::get('dashboard.dir_select');
    }
}
