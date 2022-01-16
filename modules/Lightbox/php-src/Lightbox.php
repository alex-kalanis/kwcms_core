<?php

namespace KWCMS\modules\Lightbox;


use kalanis\kw_modules\AModule;
use kalanis\kw_modules\Output;
use kalanis\kw_scripts\Scripts;
use kalanis\kw_styles\Styles;


/**
 * Class Lightbox
 * @package KWCMS\modules\Lightbox
 * Lightbox as module
 */
class Lightbox extends AModule
{
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
