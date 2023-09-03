<?php

namespace KWCMS\modules\Jquery\Controllers;


use kalanis\kw_modules\Output;
use kalanis\kw_scripts\Scripts;
use KWCMS\modules\Core\Libs\AModule;


/**
 * Class Jquery
 * @package KWCMS\modules\Jquery\Controllers
 * JQuery as module
 */
class Jquery extends AModule
{
    public function __construct(...$constructParams)
    {
    }

    public function process(): void
    {
    }

    public function output(): Output\AOutput
    {
        Scripts::want('Jquery', 'jquery-3.6.0.min.js');
        return new Output\Html();
    }
}
