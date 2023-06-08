<?php

namespace KWCMS\modules\Admin\AdminControllers;


use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_langs\Support;
use kalanis\kw_modules\AModule;
use kalanis\kw_modules\Output;


/**
 * Class LangChange
 * @package KWCMS\modules\Admin\AdminControllers
 * Change language in system
 */
class LangChange extends AModule
{
    protected $session = null;

    public function __construct()
    {
        $this->session = new SessionAdapter();
    }

    public function process(): void
    {
        $inputs = $this->inputs->getInArray(Support::LANG_KEY, [IEntry::SOURCE_GET, IEntry::SOURCE_POST, IEntry::SOURCE_CLI]);
        if (!empty($inputs)) {
            Support::setToArray($this->session, strval(reset($inputs)));
        }
    }

    public function output(): Output\AOutput
    {
        $out = new Output\Json();
        $out->setContent(['change' => 'ok', 'lang' => Support::fillFromArray($this->session, '')]);
        return $out;
    }
}
