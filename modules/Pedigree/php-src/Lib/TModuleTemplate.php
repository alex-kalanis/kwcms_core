<?php

namespace KWCMS\modules\Pedigree\Lib;


use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_routed_paths\StoreRouted;
use KWCMS\modules\Core\Libs\ExternalLink;


/**
 * Trait TModuleTemplate
 * @package KWCMS\modules\Short\Lib
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
        Lang::load('Pedigree');
        Lang::load('Admin');
        \kalanis\kw_pedigree\Config::init();
        $this->links = new ExternalLink(StoreRouted::getPath());
    }

    protected function outModuleTemplate(string $content): string
    {
        $tmpl = new ModuleTemplate();
        return $tmpl->setData(
            $content,
            $this->links->linkVariant('pedigree/dashboard'),
            $this->links->linkVariant('pedigree/add')
        )->render();
    }
}
