<?php

namespace KWCMS\modules\Personal\Templates;


use kalanis\kw_langs\Lang;
use kalanis\kw_modules\Linking\ExternalLink;
use kalanis\kw_paths\Stored;
use kalanis\kw_routed_paths\StoreRouted;


/**
 * Trait TModuleTemplate
 * @package KWCMS\modules\Personal\Templates
 */
trait TModuleTemplate
{
    /** @var ExternalLink|null */
    protected $links = null;

    public function initTModuleTemplate()
    {
        Lang::load('Personal');
        Lang::load('Admin');
        $this->links = new ExternalLink(Stored::getPath(), StoreRouted::getPath());
    }

    protected function outModuleTemplate(string $content): string
    {
        $tmpl = new ModuleTemplate();
        return $tmpl->setData(
            $content,
            $this->links->linkVariant('personal'),
            $this->links->linkVariant('personal/pass')
        )->render();
    }
}
