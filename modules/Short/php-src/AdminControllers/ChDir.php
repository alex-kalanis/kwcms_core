<?php

namespace KWCMS\modules\Short\AdminControllers;


use kalanis\kw_confs\ConfException;
use kalanis\kw_confs\Config;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Output;
use KWCMS\modules\Short\Lib;


/**
 * Class ChDir
 * @package KWCMS\modules\Short\AdminControllers
 * Change directory - by short messages
 */
class ChDir extends \KWCMS\modules\Admin\AdminControllers\ChDir implements IModuleTitle
{
    use Lib\TModuleTemplate;

    /**
     * ChDir constructor.
     * @throws ConfException
     * @throws LangException
     */
    public function __construct()
    {
        parent::__construct();
        $this->initTModuleTemplate();
        Config::load('Short');
    }

    protected function htmlContent(string $content): Output\AOutput
    {
        $out = new Output\Html();
        return $out->setContent($this->outModuleTemplate($content));
    }

    public function getTitle(): string
    {
        return Lang::get('short.page') . ' - ' . Lang::get('dashboard.dir_select');
    }
}
