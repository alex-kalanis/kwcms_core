<?php

namespace KWCMS\modules\Upload\AdminControllers;


use kalanis\kw_langs\Lang;
use kalanis\kw_modules\Output;
use KWCMS\modules\Core\Interfaces\Modules\IHasTitle;
use KWCMS\modules\Upload\Lib;


/**
 * Class ChDir
 * @package KWCMS\modules\Upload\AdminControllers
 * Change directory - by Upload
 */
class ChDir extends \KWCMS\modules\Admin\AdminControllers\ChDir implements IHasTitle
{
    use Lib\TModuleTemplate;

    public function __construct(...$constructParams)
    {
        parent::__construct(...$constructParams);
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
