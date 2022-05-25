<?php

namespace KWCMS\modules\Pedigree\Lib;


use kalanis\kw_confs\Config;
use kalanis\kw_langs\Lang;
use kalanis\kw_modules\Linking\ExternalLink;


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
        Lang::load('Pedigree');
        Lang::load('Admin');
        \kalanis\kw_pedigree\Config::init();
        $this->links = new ExternalLink(Config::getPath());
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
