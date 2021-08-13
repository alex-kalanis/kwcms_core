<?php

namespace KWCMS\modules\Errors\Lib;


use kalanis\kw_langs\Lang;
use kalanis\kw_modules\Output\Html;


/**
 * Class OutHtml
 * @package KWCMS\modules\Errors\Lib
 */
class OutHtml extends Html
{
    /** @var Template */
    protected $template = null;

    public function __construct()
    {
        $this->template = new Template();
    }

    public function setContent(string $content = '')
    {
        $this->template->reset()->setData($content, Lang::get('error.desc.' . $content));
        return $this;
    }

    public function output(): string
    {
        return $this->template->render();
    }
}
