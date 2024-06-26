<?php

namespace KWCMS\modules\Files\Lib;


use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_routed_paths\StoreRouted;
use KWCMS\modules\Core\Libs\ExternalLink;


/**
 * Trait TModuleTemplate
 * @package KWCMS\modules\Files\Lib
 */
trait TModuleTemplate
{
    /** @var ExternalLink */
    protected ?ExternalLink $links = null;

    /**
     * @throws LangException
     */
    public function initTModuleTemplate(): void
    {
        Lang::load('Files');
        Lang::load('Admin');
        $this->links = new ExternalLink(StoreRouted::getPath());
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
            $this->links->linkVariant('files/file/klone'),
            $this->links->linkVariant('files/file/rename'),
            $this->links->linkVariant('files/file/delete'),
            $this->links->linkVariant('files/file/read'),
            $this->links->linkVariant('files/dir/create'),
            $this->links->linkVariant('files/dir/copy'),
            $this->links->linkVariant('files/dir/move'),
            $this->links->linkVariant('files/dir/clone'),
            $this->links->linkVariant('files/dir/rename'),
            $this->links->linkVariant('files/dir/delete'),
            $this->links->linkVariant('files/ch-dir')
        )->render();
    }
}
