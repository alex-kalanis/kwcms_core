<?php

namespace KWCMS\modules\Iframe\Controllers;


use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_modules\Output\AOutput;
use kalanis\kw_modules\Output\Html;
use KWCMS\modules\Core\Libs\AModule;
use KWCMS\modules\Iframe\Lib;


/**
 * Class Iframe
 * @package KWCMS\modules\Iframe\Controllers
 * Iframe as page filler
 */
class Iframe extends AModule
{
    protected Lib\Template $template;
    protected string $link = '';

    /**
     * @param mixed ...$constructParams
     * @throws LangException
     */
    public function __construct(...$constructParams)
    {
        Lang::load(static::getClassName(static::class));
        $this->template = new Lib\Template();
    }

    public function process(): void
    {
        $this->link = isset($this->params['link']) ? $this->params['link'] : '' ;
    }

    public function output(): AOutput
    {
        $out = new Html();
        if (empty($this->link)) {
            $out->setContent(Lang::get('iframe.link_not_set'));
        } else {
            $out->setContent($this->template->setData($this->link)->render());
        }
        return $out;
    }
}
