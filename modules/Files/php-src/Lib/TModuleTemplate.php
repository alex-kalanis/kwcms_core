<?php

namespace KWCMS\modules\Files\Lib;


use kalanis\kw_langs\Lang;
use kalanis\kw_modules\Linking\ExternalLink;
use kalanis\kw_paths\Stored;


/**
 * Trait TModuleTemplate
 * @package KWCMS\modules\Files\Lib
 */
trait TModuleTemplate
{
    /** @var ExternalLink|null */
    protected $links = null;

    public function initTModuleTemplate()
    {
        Lang::load('Files');
        Lang::load('Admin');
        $this->links = new ExternalLink(Stored::getPath());
    }

    protected function outModuleTemplate(string $content): string
    {
        $tmpl = new ModuleTemplate();
        return $tmpl->setData(
            $content,
            $this->links->linkVariant('files/dashboard'),
            $this->links->linkVariant('files/file/upload'),
            $this->links->linkVariant('files/file/copy'),
            $this->links->linkVariant('files/file/move'),
            $this->links->linkVariant('files/file/rename'),
            $this->links->linkVariant('files/file/delete'),
            $this->links->linkVariant('files/file/read'),
            $this->links->linkVariant('files/dir/create'),
            $this->links->linkVariant('files/dir/copy'),
            $this->links->linkVariant('files/dir/move'),
            $this->links->linkVariant('files/dir/rename'),
            $this->links->linkVariant('files/dir/delete'),
            $this->links->linkVariant('files/ch-dir')
        )->render();
    }
}
