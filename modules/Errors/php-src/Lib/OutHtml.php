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
    protected Template $template;
    protected ExternalLink $linkExternal;

    public function __construct()
    {
        $this->template = new Template();
        $this->linkExternal = new ExternalLink(StoreRouted::getPath());
    }

    public function setContent(string $content = '', string $specificDescription = ''): parent
    {
        $imgLink = $this->linkExternal->linkVariant('system/alert.png', 'sysimage', true);
        $this->template->reset()->setData(
            $content,
            Lang::get('error.desc.' . $content),
            $imgLink,
            $specificDescription
        );
        return $this;
    }

    public function output(): string
    {
        return $this->template->render();
    }
}
