<?php

namespace KWCMS\modules\Short\Lib;


use kalanis\kw_langs\Lang;
use kalanis\kw_modules\Linking\ExternalLink;
use kalanis\kw_paths\Stored;


/**
 * Trait TModuleTemplate
 * @package KWCMS\modules\Short\Lib
 */
trait TModuleTemplate
{
    /** @var ExternalLink|null */
    protected $links = null;

    public function initTModuleTemplate()
    {
        Lang::load('Short');
        Lang::load('Admin');
        $this->links = new ExternalLink(Stored::getPath());
    }

    protected function outModuleTemplate(string $content): string
    {
        $tmpl = new ModuleTemplate();
        return $tmpl->setData(
            $content,
            $this->links->linkVariant('short/add'),
            $this->links->linkVariant('short/dashboard'),
            $this->links->linkVariant('short/ch-dir')
        )->render();
    }
}
