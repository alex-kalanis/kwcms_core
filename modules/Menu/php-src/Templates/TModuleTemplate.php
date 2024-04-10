<?php

namespace KWCMS\modules\Menu\Templates;


use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_routed_paths\RoutedPath;
use KWCMS\modules\Core\Libs\ExternalLink;


/**
 * Trait TModuleTemplate
 * @package KWCMS\modules\Menu\Templates
 */
trait TModuleTemplate
{
    /** @var ExternalLink */
    protected ?ExternalLink $links = null;

    /**
     * @param RoutedPath $routedPath
     * @throws LangException
     */
    public function initTModuleTemplate(RoutedPath $routedPath): void
    {
        Lang::load('Menu');
        Lang::load('Admin');
        $this->links = new ExternalLink($routedPath);
    }

    protected function outModuleTemplate(string $content): string
    {
        $tmpl = new ModuleTemplate();
        return $tmpl->setData(
            $content,
            $this->links->linkVariant('menu/dashboard'),
            $this->links->linkVariant('menu/names'),
            $this->links->linkVariant('menu/positions'),
            $this->links->linkVariant('menu/ch-dir')
        )->render();
    }
}
