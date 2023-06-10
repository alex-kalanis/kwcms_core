<?php

namespace KWCMS\modules\Menu\Templates;


use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_modules\Linking\ExternalLink;
use kalanis\kw_paths\Path;
use kalanis\kw_routed_paths\RoutedPath;


/**
 * Trait TModuleTemplate
 * @package KWCMS\modules\Menu\Templates
 */
trait TModuleTemplate
{
    /** @var ExternalLink|null */
    protected $links = null;

    /**
     * @param Path $path
     * @param RoutedPath $routedPath
     * @throws LangException
     */
    public function initTModuleTemplate(Path $path, RoutedPath $routedPath)
    {
        Lang::load('Menu');
        Lang::load('Admin');
        $this->links = new ExternalLink($path, $routedPath);
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
