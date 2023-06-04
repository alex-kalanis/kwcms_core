<?php

namespace KWCMS\modules\Texts;


use kalanis\kw_langs\Lang;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Output;
use kalanis\kw_paths\Stored;
use kalanis\kw_routed_paths\StoreRouted;


/**
 * Class ChDir
 * @package KWCMS\modules\Texts
 * Change directory - by texts
 */
class ChDir extends \KWCMS\modules\Admin\ChDir implements IModuleTitle
{
    use Lib\TModuleTemplate;

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
        return Lang::get('texts.page') . ' - ' . Lang::get('dashboard.dir_select');
    }
}
