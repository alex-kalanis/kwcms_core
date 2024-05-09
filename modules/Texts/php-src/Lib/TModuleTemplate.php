<?php

namespace KWCMS\modules\Texts\Lib;


use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_routed_paths\RoutedPath;
use KWCMS\modules\Core\Libs\ExternalLink;


/**
 * Trait TModuleTemplate
 * @package KWCMS\modules\Texts\Lib
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
        Lang::load('Texts');
        Lang::load('Admin');
        $this->links = new ExternalLink($routedPath);
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
