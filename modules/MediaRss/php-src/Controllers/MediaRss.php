<?php

namespace KWCMS\modules\MediaRss\Controllers;


use kalanis\kw_confs\Config;
use kalanis\kw_files\Access\Factory;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Node;
use kalanis\kw_images\Content\Images;
use kalanis\kw_images\FilesHelper;
use kalanis\kw_images\ImagesException;
use kalanis\kw_modules\AModule;
use kalanis\kw_modules\Interfaces\ISitePart;
use kalanis\kw_modules\Linking\ExternalLink;
use kalanis\kw_modules\Output;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stored;
use kalanis\kw_paths\Stuff;
use kalanis\kw_routed_paths\StoreRouted;
use kalanis\kw_tree\DataSources\Files;
use kalanis\kw_tree\Essentials\FileNode;
use kalanis\kw_tree\Interfaces\ITree;
use kalanis\kw_user_paths\InnerLinks;
use KWCMS\modules\Images\Lib\TLibAction;
use KWCMS\modules\MediaRss\Lib;


/**
 * Class MediaRss
 * @package KWCMS\modules\MediaRss\Controllers
 * Site's MediaRss feed - images in path
 */
class MediaRss extends AModule
{
    use TLibAction;

    /** @var ExternalLink */
    protected $libExternal = null;
    /** @var ArrayPath */
    protected $arrPath = null;
    /** @var InnerLinks */
    protected $innerLink = null;
    /** @var Images */
    protected $sources = null;

    public function __construct()
    {
        Config::load(static::getClassName(static::class));
        $this->libExternal = new ExternalLink(Stored::getPath(), StoreRouted::getPath());
        $this->arrPath = new ArrayPath();
        $this->innerLink = new InnerLinks(
            StoreRouted::getPath(),
            boolval(Config::get('Core', 'site.more_users', false)),
            boolval(Config::get('Core', 'page.more_lang', false))
        );
        $this->sources = FilesHelper::getImages(Stored::getPath()->getDocumentRoot() . Stored::getPath()->getPathToSystemRoot());
    }

    public function process(): void
    {
    }

    protected function getUserDir(): string
    {
        return '';
    }

    protected function getWhereDir(): string
    {
        return '';
    }

    public function output(): Output\AOutput
    {
        return ($this->params[ISitePart::KEY_LEVEL] == ISitePart::SITE_RESPONSE) ? $this->outResponse() : $this->outLink() ;
    }

    public function outLink(): Output\AOutput
    {
        $template = new Lib\HeadTemplate();
        $out = new Output\Html();
        return $out->setContent($template->setData($this->libExternal->linkVariant('', 'media-rss', true))->render());
    }

    public function outResponse(): Output\AOutput
    {
        Config::load('Core', 'page');
        Config::load('Logo');
        $out = new Output\Raw();
        try {
            $libTree = new Files((new Factory())->getClass(Stored::getPath()->getDocumentRoot() . Stored::getPath()->getPathToSystemRoot()));

            $tmpl = new Lib\MainTemplate();
            return $out->setContent(
                $tmpl->setData(
                    $this->libExternal->linkVariant('rss/rss-style.css', 'styles', true, false),
                    Config::get('Core', 'page.site_name'),
                    $this->libExternal->linkVariant(),
                    $this->getLibDirAction()->getDesc()
                )->addItems(
                    implode('', $this->getItems($libTree))
                )->render()
            );
        } catch (ImagesException | FilesException | PathsException $ex) {
            $error = $ex;
        }
        if (isset($error)) {
            return $out->setContent($error->getMessage());
        }
        return $out;
    }

    /**
     * @param ITree $libTree
     * @throws FilesException
     * @throws PathsException
     * @return string[]
     */
    protected function getItems(ITree $libTree): array
    {
        $tmplItem = new Lib\ItemTemplate();
        $messages = [];
        $passedPath = StoreRouted::getPath()->getPath();
        $realPath = $this->innerLink->toFullPath($passedPath);
        if (!$this->sources->exists($realPath)) {
            return $messages;
        }
        $libTree->setStartPath($realPath);
        $libTree->wantDeep(false);
        $libTree->setFilterCallback([$this, 'filterImages']);
        $libTree->process();
        if ($libTree->getRoot()) {
            foreach ($libTree->getRoot()->getSubNodes() as $item) {
                /** @var FileNode $item */
                $strPath = $this->arrPath->setArray($item->getPath())->getString();
                $desc = $this->sources->getDescription($item->getPath());
                $messages[] = $tmplItem->reset()->setData(
                    $this->libExternal->linkVariant($strPath, 'image', true),
                    $desc,
                    $desc,
                    $this->libExternal->linkVariant($this->sources->reverseThumbPath($item->getPath()), 'image', true),
                    $this->libExternal->linkVariant($strPath, 'image', true)
                )->render();
            }
        }
        return $messages;
    }

    public function filterImages(Node $info): bool
    {
        $name = array_slice($info->getPath(), -1, 1);
        $name = reset($name);
        return in_array(Stuff::fileExt($name), (array)Config::get(static::getClassName(static::class), 'accept_types', []));
    }
}
