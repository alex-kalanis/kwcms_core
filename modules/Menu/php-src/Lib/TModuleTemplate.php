<?php

namespace KWCMS\modules\Menu\Lib;


use kalanis\kw_langs\Lang;
use kalanis\kw_modules\ExternalLink;
use kalanis\kw_paths\Path;


/**
 * Trait TModuleTemplate
 * @package KWCMS\modules\Menu\Lib
 */
trait TModuleTemplate
{
    /** @var ExternalLink|null */
    protected $links = null;

    public function initTModuleTemplate(Path $path)
    {
        Lang::load('Menu');
        Lang::load('Admin');
        $this->links = new ExternalLink($path);
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
