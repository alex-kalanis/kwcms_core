<?php

namespace KWCMS\modules\Images\Templates;


use kalanis\kw_confs\Config;
use kalanis\kw_langs\Lang;
use kalanis\kw_modules\ExternalLink;


/**
 * Trait TModuleTemplate
 * @package KWCMS\modules\Images\Templates
 */
trait TModuleTemplate
{
    /** @var ExternalLink|null */
    protected $links = null;

    public function initTModuleTemplate()
    {
        Lang::load('Images');
        Lang::load('Admin');
        $this->links = new ExternalLink(Config::getPath());
    }

    protected function outModuleTemplate(string $content): string
    {
        $tmpl = new ModuleTemplate();
        return $tmpl->setData(
            $content,
            $this->links->linkVariant('images/dashboard'),
            $this->links->linkVariant('images/properties'),
            $this->links->linkVariant('images/make-dir'),
            $this->links->linkVariant('images/upload'),
            $this->links->linkVariant('images/ch-dir')
        )->render();
    }
}
