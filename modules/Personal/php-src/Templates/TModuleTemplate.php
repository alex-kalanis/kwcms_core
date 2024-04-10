<?php

namespace KWCMS\modules\Personal\Templates;


use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_routed_paths\StoreRouted;
use KWCMS\modules\Core\Libs\ExternalLink;


/**
 * Trait TModuleTemplate
 * @package KWCMS\modules\Personal\Templates
 */
trait TModuleTemplate
{
    /** @var ExternalLink */
    protected ?ExternalLink $links = null;

    /**
     * @throws LangException
     */
    public function initTModuleTemplate(): void
    {
        Lang::load('Personal');
        Lang::load('Admin');
        $this->links = new ExternalLink(StoreRouted::getPath());
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
