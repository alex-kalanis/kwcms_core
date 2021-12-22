<?php

namespace KWCMS\modules\Chsett\Templates;


use kalanis\kw_confs\Config;
use kalanis\kw_langs\Lang;
use kalanis\kw_modules\ExternalLink;


/**
 * Trait TModuleTemplate
 * @package KWCMS\modules\Chsett\Templates
 */
trait TModuleTemplate
{
    /** @var ExternalLink|null */
    protected $links = null;

    public function initTModuleTemplate()
    {
        Lang::load('Chsett');
        Lang::load('Admin');
        $this->links = new ExternalLink(Config::getPath());
    }

    protected function outModuleTemplate(string $content): string
    {
        $tmpl = new ModuleTemplate();
        return $tmpl->setData(
            $content,
            $this->links->linkVariant('chsett/dashboard'),
            $this->links->linkVariant('chsett/user/add'),
            $this->links->linkVariant('chsett/groups'),
            $this->links->linkVariant('chsett/group/add')
        )->render();
    }
}