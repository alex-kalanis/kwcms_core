<?php

namespace KWCMS\modules\Lightbox\Controllers;


use kalanis\kw_modules\Output;
use kalanis\kw_scripts\Scripts;
use kalanis\kw_styles\Styles;
use KWCMS\modules\Core\Libs\AModule;


/**
 * Class Lightbox
 * @package KWCMS\modules\Lightbox\Controllers
 * Lightbox as module
 */
class Lightbox extends AModule
{
    public function __construct(...$constructParams)
    {
    }

    public function process(): void
    {
    }

    public function output(): Output\AOutput
    {
        Scripts::want('Lightbox', 'lightbox.js');
        Styles::want('Lightbox', 'lightbox.css');
        return new Output\Html();
    }
}
