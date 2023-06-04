<?php

namespace KWCMS\modules\MediaRss;


use kalanis\kw_confs\Config;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Node;
use kalanis\kw_files\Processing\Volume\ProcessDir;
use kalanis\kw_images\ImagesException;
use kalanis\kw_modules\AModule;
use kalanis\kw_modules\Interfaces\ISitePart;
use kalanis\kw_modules\Linking\ExternalLink;
use kalanis\kw_modules\Linking\InternalLink;
use kalanis\kw_modules\Output;
use kalanis\kw_paths\Extras\UserDir;
use kalanis\kw_paths\Stored;
use kalanis\kw_paths\Stuff;
use kalanis\kw_routed_paths\StoreRouted;
use kalanis\kw_tree\FileNode;
use kalanis\kw_tree\Tree;
use KWCMS\modules\Images\Interfaces\IProcessFiles;
use KWCMS\modules\Images\Lib\TLibAction;


/**
 * Class MediaRss
 * @package KWCMS\modules\Rss
 * Site's MediaRss feed - images in path
 */
class MediaRss extends AModule
{
    use TLibAction;

    /** @var UserDir|null */
    protected $userDir = null;
    /** @var ExternalLink|null */
    protected $libExternal = null;
    /** @var InternalLink|null */
    protected $libInternal = null;

    public function __construct()
    {
        Config::load(static::getClassName(static::class));
        $this->userDir = new UserDir(Stored::getPath());
        $this->libExternal = new ExternalLink(Stored::getPath(), StoreRouted::getPath());
        $this->libInternal = new InternalLink(Stored::getPath(), StoreRouted::getPath());
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
            $libTree = new Tree(Stored::getPath(), new ProcessDir());

            $tmpl = new Lib\MainTemplate();
            return $out->setContent($tmpl->setData(
                $this->libExternal->linkVariant('rss/rss-style.css', 'styles', true, false),
                Config::get('Core', 'page.site_name'),
                $this->libExternal->linkVariant(),
                $this->getLibDirAction()->getDesc()
            )->addItems(
                implode('', $this->getItems($libTree, $this->getLibFileAction()))
            )->render()
            );
        } catch (ImagesException | FilesException $ex) {
            $error = $ex;
        }
        if (isset($error)) {
            return $out->setContent($error->getMessage());
        }
        return $out;
    }

    /**
     * @param Tree $libTree
     * @param IProcessFiles $libFiles
     * @throws FilesException
     * @return string[]
     */
    protected function getItems(Tree $libTree, IProcessFiles $libFiles): array
    {
        $tmplItem = new Lib\ItemTemplate();
        $messages = [];
        $passedPath = StoreRouted::getPath()->getPath();
        $realPath = $this->libInternal->shortContent($passedPath);
        if (empty($realPath)) {
            return $messages;
        }
        $libTree->startFromPath($realPath);
        $libTree->canRecursive(false);
        $libTree->setFilterCallback([$this, 'filterImages']);
        $libTree->process();
        if ($libTree->getTree()) {
            foreach ($libTree->getTree()->getSubNodes() as $item) {
                /** @var FileNode $item */
                $path = $realPath . '/' . $item->getName();
                $desc = $libFiles->readDesc($path);
                $messages[] = $tmplItem->reset()->setData(
                    $this->libExternal->linkVariant($passedPath . '/' . $item->getName(), 'image', true),
                    $desc,
                    $desc,
                    $this->libExternal->linkVariant($libFiles->reverseThumb($path), 'image', true),
                    $this->libExternal->linkVariant($passedPath . '/' . $item->getName(), 'image', true)
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
