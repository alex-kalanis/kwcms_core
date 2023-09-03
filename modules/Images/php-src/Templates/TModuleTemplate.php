<?php

namespace KWCMS\modules\Images\Templates;


use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_routed_paths\StoreRouted;
use KWCMS\modules\Core\Libs\ExternalLink;


/**
 * Trait TModuleTemplate
 * @package KWCMS\modules\Images\Templates
 */
trait TModuleTemplate
{
    /** @var ExternalLink|null */
    protected $links = null;

    /**
     * @throws LangException
     */
    public function initTModuleTemplate()
    {
        Lang::load('Images');
        Lang::load('Admin');
        $this->links = new ExternalLink(StoreRouted::getPath());
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
