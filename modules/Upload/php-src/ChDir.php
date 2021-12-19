<?php

namespace KWCMS\modules\Upload;


use kalanis\kw_langs\Lang;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Output;
use KWCMS\modules\Admin\Shared;


/**
 * Class ChDir
 * @package KWCMS\modules\Upload
 * Change directory - by Upload
 */
class ChDir extends \KWCMS\modules\Admin\ChDir implements IModuleTitle
{
    use Lib\TModuleTemplate;

    public function __construct()
    {
        parent::__construct();
        $this->initTModuleTemplate();
    }

    protected function htmlContent(string $content): Output\AOutput
    {
        $out = new Shared\FillHtml($this->user);
        return $out->setContent($this->outModuleTemplate($content));
    }

    public function getTitle(): string
    {
        return Lang::get('upload.page') . ' - ' . Lang::get('dashboard.dir_select');
    }
}