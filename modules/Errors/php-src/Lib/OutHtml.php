<?php

namespace KWCMS\modules\Errors\Lib;


use kalanis\kw_langs\Lang;
use kalanis\kw_modules\Output\Html;
use kalanis\kw_routed_paths\StoreRouted;
use KWCMS\modules\Core\Libs\ExternalLink;


/**
 * Class OutHtml
 * @package KWCMS\modules\Errors\Lib
 */
class OutHtml extends Html
{
    /** @var Template */
    protected $template = null;
    /** @var ExternalLink */
    protected $linkExternal = null;

    public function __construct()
    {
        $this->template = new Template();
        $this->linkExternal = new ExternalLink(StoreRouted::getPath());
    }

    public function setContent(string $content = ''): parent
    {
        $imgLink = $this->linkExternal->linkVariant('files/alert.png', 'sysimage', true);
        $this->template->reset()->setData($content, Lang::get('error.desc.' . $content), $imgLink);
        return $this;
    }

    public function output(): string
    {
        return $this->template->render();
    }
}
