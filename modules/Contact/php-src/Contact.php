<?php

namespace KWCMS\modules\Contact;


use kalanis\kw_confs\Config;
use kalanis\kw_modules\AModule;
use kalanis\kw_modules\Output\AOutput;
use kalanis\kw_modules\Output\Html;


/**
 * Class Contact
 * @package KWCMS\modules\Contact
 * Contact as module
 */
class Contact extends AModule
{
    public function process(): void
    {
    }

    public function output(): AOutput
    {
        $out = new Html();
        return $out->setContent(Config::get('Core', 'page.contact'));
    }
}
