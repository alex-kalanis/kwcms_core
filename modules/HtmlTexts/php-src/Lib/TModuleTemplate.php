<?php

namespace KWCMS\modules\HtmlTexts\Lib;


use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_routed_paths\RoutedPath;
use KWCMS\modules\Core\Libs\ExternalLink;
use KWCMS\modules\Texts\Lib\ModuleTemplate;


/**
 * Trait TModuleTemplate
 * @package KWCMS\modules\HtmlTexts\Lib
 */
trait TModuleTemplate
{
    /** @var ExternalLink|null */
    protected $links = null;

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
            $this->links->linkVariant('html-texts/dashboard'),
            $this->links->linkVariant('html-texts/ch-dir')
        )->render();
    }
}
