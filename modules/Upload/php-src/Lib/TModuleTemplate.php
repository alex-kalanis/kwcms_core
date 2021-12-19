<?php

namespace KWCMS\modules\Upload\Lib;


use kalanis\kw_confs\Config;
use kalanis\kw_langs\Lang;
use kalanis\kw_modules\ExternalLink;


/**
 * Trait TModuleTemplate
 * @package KWCMS\modules\Upload\Lib
 */
trait TModuleTemplate
{
    /** @var ExternalLink|null */
    protected $links = null;

    public function initTModuleTemplate()
    {
        Lang::load('Upload');
        Lang::load('Admin');
        $this->links = new ExternalLink(Config::getPath());
    }

    protected function outModuleTemplate(string $content): string
    {
        $tmpl = new ModuleTemplate();
        return $tmpl->setData(
            $content,
            $this->links->linkVariant('upload/dashboard'),
            $this->links->linkVariant('upload/ch-dir')
        )->render();
    }
}
