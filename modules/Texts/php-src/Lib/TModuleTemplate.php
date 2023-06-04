<?php

namespace KWCMS\modules\Texts\Lib;


use kalanis\kw_langs\Lang;
use kalanis\kw_modules\Linking\ExternalLink;
use kalanis\kw_paths\Path;
use kalanis\kw_routed_paths\RoutedPath;


/**
 * Trait TModuleTemplate
 * @package KWCMS\modules\Texts\Lib
 */
trait TModuleTemplate
{
    /** @var ExternalLink|null */
    protected $links = null;

    public function initTModuleTemplate(Path $path, RoutedPath $routedPath)
    {
        Lang::load('Texts');
        Lang::load('Admin');
        $this->links = new ExternalLink($path, $routedPath);
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
