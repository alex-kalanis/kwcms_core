<?php

namespace KWCMS\modules\MediaRss\Controllers;


use kalanis\kw_confs\ConfException;
use kalanis\kw_confs\Config;
use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_files\Access\Factory as files_factory;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Node;
use kalanis\kw_images\Access\Factory as images_factory;
use kalanis\kw_images\Content\Images;
use kalanis\kw_images\ImagesException;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_modules\Interfaces\Lists\ISitePart;
use kalanis\kw_modules\Output;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;
use kalanis\kw_routed_paths\StoreRouted;
use kalanis\kw_tree\DataSources\Files;
use kalanis\kw_tree\Essentials\FileNode;
use kalanis\kw_tree\Interfaces\ITree;
use kalanis\kw_user_paths\InnerLinks;
use KWCMS\modules\Core\Libs\AModule;
use KWCMS\modules\Core\Libs\ExternalLink;
use KWCMS\modules\Core\Libs\FilesTranslations;
use KWCMS\modules\Core\Libs\ImagesTranslations;
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

    protected ExternalLink $libExternal;
    protected CompositeAdapter $files;
    protected ArrayPath $arrPath;
    protected InnerLinks $innerLink;
    protected Images $sources;
    /** @var string[] */
    protected array $acceptTypes = [];

    /**
     * @param mixed ...$constructParams
     * @throws ConfException
     * @throws FilesException
     * @throws ImagesException
     * @throws LangException
     * @throws PathsException
     */
    public function __construct(...$constructParams)
    {
        Config::load(static::getClassName(static::class));
        Lang::load(static::getClassName(static::class));
        $this->libExternal = new ExternalLink(StoreRouted::getPath());
        $this->arrPath = new ArrayPath();
        $this->innerLink = new InnerLinks(
            StoreRouted::getPath(),
            boolval(Config::get('Core', 'site.more_users', false)),
            boolval(Config::get('Core', 'page.more_lang', false)),
            [],
            boolval(Config::get('Core', 'page.system_prefix', false)),
            boolval(Config::get('Core', 'page.data_separator', false))
        );
        $this->files = (new files_factory(new FilesTranslations()))->getClass($constructParams);
        $this->sources = (new images_factory(
            $this->files,
            null,
            new ImagesTranslations()
        ))->getImages($constructParams);

        $this->acceptTypes = (array) Config::get(static::getClassName(static::class), 'accept_types', []);
    }

    public function process(): void
    {
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
        $out = new Output\Raw();
        try {
            Config::load('Core', 'page');

            $userPath = array_filter($this->innerLink->toFullPath([]));
            $currentPath = StoreRouted::getPath()->getPath();
            $last = array_pop($currentPath);
            $last = is_null($last) ? '' : Stuff::fileBase($last);
            $currentPath[] = $last;

            $tmpl = new Lib\MainTemplate();
            return $out->setContent(
                $tmpl->setData(
                    $this->libExternal->linkVariant('rss/rss-style.css', 'styles', true, false),
                    Config::get('Core', 'page.site_name'),
                    $this->libExternal->linkVariant(StoreRouted::getPath()->getPath()),
                    $this->getLibDirAction($this->files, $userPath, $currentPath)->getDesc()
                )->addItems(
                    implode('', $this->getItems(new Files($this->files), $userPath, $currentPath))
                )->render()
            );
        } catch (ConfException | ImagesException | FilesException | PathsException $ex) {
            $error = $ex;
        }
        if (isset($error)) {
            return $out->setContent($error->getMessage());
        }
        return $out;
    }

    /**
     * @param ITree $libTree
     * @param string[] $userPath
     * @param string[] $currentPath
     * @throws FilesException
     * @throws PathsException
     * @return string[]
     */
    protected function getItems(ITree $libTree, array $userPath, array $currentPath): array
    {
        $tmplItem = new Lib\ItemTemplate();
        $messages = [];
        $libTree->setStartPath(array_merge($userPath, $currentPath));
        $libTree->wantDeep(false);
        $libTree->setFilterCallback([$this, 'filterImages']);
        $libTree->process();
        if ($libTree->getRoot()) {
            foreach ($libTree->getRoot()->getSubNodes() as $item) {
                /** @var FileNode $item */
                $strPath = $this->arrPath->setArray(array_merge($currentPath, $item->getPath()))->getString();
                $desc = $this->sources->getDescription(array_merge($userPath, $currentPath, $item->getPath()));
                $messages[] = $tmplItem->reset()->setData(
                    $this->libExternal->linkVariant($strPath, 'image', true),
                    $desc,
                    $desc,
                    $this->libExternal->linkVariant(Stuff::arrayToLink($this->sources->reverseThumbPath(
                        array_merge($currentPath, $item->getPath())
                    )), 'image', true),
                    $this->libExternal->linkVariant($strPath, 'image', true)
                )->render();
            }
        }
        return $messages;
    }

    public function filterImages(Node $info): bool
    {
        return in_array(Stuff::fileExt($this->arrPath->setArray($info->getPath())->getFileName()), $this->acceptTypes);
    }
}
