<?php

namespace KWCMS\modules\HtmlTexts\Lib;


use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_modules\Linking\ExternalLink;
use kalanis\kw_paths\Path;
use kalanis\kw_routed_paths\RoutedPath;
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
     * @param Path $path
     * @param RoutedPath $routedPath
     * @throws LangException
     */
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
            $this->links->linkVariant('html-texts/dashboard'),
            $this->links->linkVariant('html-texts/ch-dir')
        )->render();
    }
}
