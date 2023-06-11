<?php

namespace KWCMS\modules\Images\AdminControllers;


use kalanis\kw_confs\ConfException;
use kalanis\kw_confs\Config;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Output;
use KWCMS\modules\Images\Templates;


/**
 * Class ChDir
 * @package KWCMS\modules\Images\AdminControllers
 * Change directory - by Images
 */
class ChDir extends \KWCMS\modules\Admin\AdminControllers\ChDir implements IModuleTitle
{
    use Templates\TModuleTemplate;

    /**
     * ChDir constructor.
     * @throws ConfException
     * @throws LangException
     */
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