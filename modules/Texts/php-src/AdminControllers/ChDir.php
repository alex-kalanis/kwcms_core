<?php

namespace KWCMS\modules\Texts\AdminControllers;


use kalanis\kw_langs\Lang;
use kalanis\kw_modules\Output;
use kalanis\kw_routed_paths\StoreRouted;
use KWCMS\modules\Core\Interfaces\Modules\IHasTitle;
use KWCMS\modules\Texts\Lib;


/**
 * Class ChDir
 * @package KWCMS\modules\Texts\AdminControllers
 * Change directory - by texts
 */
class ChDir extends \KWCMS\modules\Admin\AdminControllers\ChDir implements IHasTitle
{
    use Lib\TModuleTemplate;

    public function __construct(...$constructParams)
    {
        parent::__construct(...$constructParams);
        $this->initTModuleTemplate(StoreRouted::getPath());
    }

    protected function htmlContent(string $content): Output\AOutput
    {
        $out = new Output\Html();
        return $out->setContent($this->outModuleTemplate($content));
    }

    public function getTitle(): string
    {
        return Lang::get('texts.page') . ' - ' . Lang::get('dashboard.dir_select');
    }
}
