<?php

namespace KWCMS\modules\Notify\AdminControllers;


use kalanis\kw_modules\Output\AOutput;
use kalanis\kw_modules\Output\Raw;
use kalanis\kw_scripts\Scripts;
use kalanis\kw_styles\Styles;
use KWCMS\modules\Core\Libs\AModule;


/**
 * Class Notify
 * @package KWCMS\modules\Notify\AdminControllers
 * Site's notifications - init styles and scripts
 */
class NotifyInit extends AModule
{
    public function __construct(...$constructParams)
    {
    }

    public function process(): void
    {
        Styles::want('Notify', 'notify.css');
        Scripts::want('Notify', 'notify.js');
    }

    /**
     * @return AOutput
     */
    public function output(): AOutput
    {
        return new Raw();
    }
}
