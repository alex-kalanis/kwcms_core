<?php

namespace KWCMS\modules\Texts\Lib;


use kalanis\kw_confs\Config;
use kalanis\kw_langs\Lang;
use kalanis\kw_modules\ExternalLink;


/**
 * Trait TModuleTemplate
 * @package KWCMS\modules\Texts\Lib
 */
trait TModuleTemplate
{
    /** @var ExternalLink|null */
    protected $links = null;

    public function initTModuleTemplate()
    {
        Lang::load('Texts');
        Lang::load('Admin');
        $this->links = new ExternalLink(Config::getPath());
    }

    protected function outModuleTemplate(string $content): string
    {
        $tmpl = new ModuleTemplate();
        return $tmpl->setData(
            $content,
            $this->links->linkVariant('texts/dashboard'),
            $this->links->linkVariant('texts/ch-dir')
        )->render();
    }
}
