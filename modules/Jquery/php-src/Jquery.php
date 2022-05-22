<?php

namespace KWCMS\modules\Jquery;


use kalanis\kw_modules\AModule;
use kalanis\kw_modules\Output;
use kalanis\kw_scripts\Scripts;


/**
 * Class Jquery
 * @package KWCMS\modules\Jquery
 * JQuery as module
 */
class Jquery extends AModule
{
    public function process(): void
    {
    }

    public function output(): Output\AOutput
    {
        Scripts::want('Jquery', 'jquery-3.6.0.min.js');
        return new Output\Html();
    }
}
