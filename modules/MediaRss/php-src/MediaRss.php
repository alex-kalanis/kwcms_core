<?php

namespace KWCMS\modules\MediaRss;


use kalanis\kw_confs\Config;
use kalanis\kw_images\Files;
use kalanis\kw_images\FilesHelper;
use kalanis\kw_images\ImagesException;
use kalanis\kw_modules\AModule;
use kalanis\kw_modules\ExternalLink;
use kalanis\kw_modules\Interfaces\ISitePart;
use kalanis\kw_modules\InternalLink;
use kalanis\kw_modules\Output;
use kalanis\kw_paths\Extras\UserDir;
use kalanis\kw_tree\FileNode;
use kalanis\kw_tree\Tree;


/**
 * Class MediaRss
 * @package KWCMS\modules\Rss
 * Site's MediaRss feed - images in path
 */
class MediaRss extends AModule
{
    /** @var UserDir|null */
    protected $userDir = null;
    /** @var ExternalLink|null */
    protected $libExternal = null;
    /** @var InternalLink|null */
    protected $libInternal = null;

    public function __construct()
    {
        Config::load(static::getClassName(static::class));
        $this->userDir = new UserDir(Config::getPath());
        $this->libExternal = new ExternalLink(Config::getPath());
        $this->libInternal = new InternalLink(Config::getPath());
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
        Config::load('Core', 'page');
        Config::load('Logo');
        $out = new Output\Raw();
        try {
            $libUserDir = new UserDir(Config::getPath());
            $libFiles = FilesHelper::get($libUserDir->getWebRootDir());
            $libTree = new Tree(Config::getPath());

            $tmpl = new Lib\MainTemplate();
            return $out->setContent($tmpl->setData(
                $this->libExternal->linkVariant('rss/rss-style.css', 'styles', true, false),
                Config::get('Core', 'page.site_name'),
                $this->libExternal->linkVariant(),
                $this->getDesc($libFiles)
            )->addItems(
                implode('', $this->getItems($libTree, $libFiles))
            )->render()
            );
        } catch (ImagesException $ex) {
            $error = $ex;
        }
        if (isset($error)) {
            return $out->setContent($error->getMessage());
        }
        return $out;
    }

    /**
     * @param Tree $libTree
     * @param Files $libFiles
     * @return string[]
     * @throws ImagesException
     */
    protected function getItems(Tree $libTree, Files $libFiles): array
    {
        $tmplItem = new Lib\ItemTemplate();
        $messages = [];
        $passedPath = Config::getPath()->getPath();
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
                $messages[] = $tmplItem->reset()->setData(
                    $this->libExternal->linkVariant($passedPath . '/' . $item->getName(), 'image', true),
                    (string)$libFiles->getLibDesc()->get($realPath . DIRECTORY_SEPARATOR . $item->getName()),
                    (string)$libFiles->getLibDesc()->get($realPath . DIRECTORY_SEPARATOR . $item->getName()),
                    $this->libExternal->linkVariant($libFiles->getLibThumb()->getPath($passedPath . DIRECTORY_SEPARATOR . $item->getName()), 'image', true),
                    $this->libExternal->linkVariant($passedPath . '/' . $item->getName(), 'image', true)
                )->render();
            }
        }
        return $messages;
    }

    public function filterImages(\SplFileInfo $info): bool
    {
        return in_array($info->getExtension(), (array)Config::get(static::getClassName(static::class), 'accept_types', []));
    }

    /**
     * @param Files $libFiles
     * @return string
     * @throws ImagesException
     */
    protected function getDesc(Files $libFiles): string
    {
        return $libFiles->getLibDirDesc()->get((string)$this->libInternal->shortContent(Config::getPath()->getPath()));
    }
}
