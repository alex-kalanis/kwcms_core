<?php

namespace KWCMS\modules\Contact\Controllers;


use kalanis\kw_confs\Config;
use kalanis\kw_modules\AModule;
use kalanis\kw_modules\Output;


/**
 * Class Contact
 * @package KWCMS\modules\Contact\Controllers
 * Contact as module
 */
class Contact extends AModule
{
    public function process(): void
    {
    }

    public function output(): Output\AOutput
    {
        $out = new Output\Html();
        return $out->setContent(Config::get('Core', 'page.contact'));
    }
}
