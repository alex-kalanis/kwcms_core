<?php

namespace KWCMS\modules\Files\AdminControllers;


use kalanis\kw_auth_sources\Interfaces\IWorkClasses;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_modules\AAuthModule;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Output;
use kalanis\kw_styles\Styles;
use KWCMS\modules\Files\Lib;


/**
 * Class Dashboard
 * @package KWCMS\modules\Files\AdminControllers
 * Site's file content - list available actions
 */
class Dashboard extends AAuthModule implements IModuleTitle
{
    use Lib\TModuleTemplate;

    /**3
     * @throws LangException
     */
    public function __construct()
    {
        $this->initTModuleTemplate();
    }

    public function allowedAccessClasses(): array
    {
        return [IWorkClasses::CLASS_MAINTAINER, IWorkClasses::CLASS_ADMIN, IWorkClasses::CLASS_USER, ];
    }

    public function run(): void
    {
    }

    public function result(): Output\AOutput
    {
        return $this->isJson()
            ? $this->outJson()
            : $this->outHtml();
    }

    public function outHtml(): Output\AOutput
    {
        $out = new Output\Html();
        if (!empty($this->error)) {
            return $out->setContent($this->outModuleTemplate($this->error->getMessage() . nl2br($this->error->getTraceAsString())));
        }
        Styles::want('Files', 'dashboard.css');
        $page = new Lib\DashboardTemplate();
        return $out->setContent($this->outModuleTemplate($page->setLinks(
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
        )->setImages(
            $this->links->linkVariant('files/file/upload.png', 'sysimage', true),
            $this->links->linkVariant('files/file/copy.png', 'sysimage', true),
            $this->links->linkVariant('files/file/move.png', 'sysimage', true),
            $this->links->linkVariant('files/file/rename.png', 'sysimage', true),
            $this->links->linkVariant('files/file/delete.png', 'sysimage', true),
            $this->links->linkVariant('files/file/read.png', 'sysimage', true),
            $this->links->linkVariant('files/dir/new.png', 'sysimage', true),
            $this->links->linkVariant('files/dir/copy.png', 'sysimage', true),
            $this->links->linkVariant('files/dir/move.png', 'sysimage', true),
            $this->links->linkVariant('files/dir/rename.png', 'sysimage', true),
            $this->links->linkVariant('files/dir/delete.png', 'sysimage', true),
            $this->links->linkVariant('files/dir/chdir.png', 'sysimage', true)
        )->render()));
    }

    public function outJson(): Output\AOutput
    {
        if ($this->error) {
            $out = new Output\JsonError();
            return $out->setContent($this->error->getCode(), $this->error->getMessage());
        } else {
            $out = new Output\Json();
            $out->setContent([
                'actions' => [
                    'file/upload',
                    'file/copy',
                    'file/move',
                    'file/rename',
                    'file/delete',
                    'file/read',
                    'dir/create',
                    'dir/copy',
                    'dir/move',
                    'dir/rename',
                    'dir/delete',
                    'ch-dir',
                ],
            ]);
            return $out;
        }
    }

    public function getTitle(): string
    {
        return Lang::get('files.page');
    }
}
