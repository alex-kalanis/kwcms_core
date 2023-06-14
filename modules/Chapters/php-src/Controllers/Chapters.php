<?php

namespace KWCMS\modules\Chapters\Controllers;


use kalanis\kw_confs\ConfException;
use kalanis\kw_confs\Config;
use kalanis\kw_files\Access\Factory;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Node;
use kalanis\kw_modules\AModule;
use kalanis\kw_modules\Linking\ExternalLink;
use kalanis\kw_modules\Output;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stored;
use kalanis\kw_routed_paths\StoreRouted;
use kalanis\kw_tree\DataSources\Files;
use kalanis\kw_tree\Essentials\FileNode;
use kalanis\kw_user_paths\InnerLinks;
use KWCMS\modules\Chapters\Lib;


/**
 * Class Chapters
 * @package KWCMS\modules\Chapters\Controllers
 * Chapters as module
 */
class Chapters extends AModule
{
    /** @var ArrayPath */
    protected $arrPath = null;
    /** @var Files */
    protected $treeList = null;
    /** @var InnerLinks */
    protected $innerLink = null;
    /** @var ExternalLink */
    protected $linkExternal = null;
    /** @var Lib\PageTemplate */
    protected $tmplPage = null;
    /** @var string */
    protected $currentFile = '';
    /** @var string */
    protected $mask = '';
    /** @var FileNode[] */
    protected $availableFiles = [];
    /** @var int|null */
    protected $position = null;

    /**
     * @throws PathsException
     * @throws ConfException
     * @throws FilesException
     */
    public function __construct()
    {
        Config::load('Chapters');
        $this->arrPath = new ArrayPath();
        $this->linkExternal = new ExternalLink(Stored::getPath(), StoreRouted::getPath());
        $this->tmplPage = new Lib\PageTemplate();
        $this->innerLink = new InnerLinks(
            StoreRouted::getPath(),
            boolval(Config::get('Core', 'site.more_users', false)),
            boolval(Config::get('Core', 'page.more_lang', false))
        );
        $this->treeList = new Files((new Factory())->getClass(
            Stored::getPath()->getDocumentRoot() . Stored::getPath()->getPathToSystemRoot()
        ));
    }

    /**
     * @throws PathsException
     */
    public function process(): void
    {
        $this->arrPath->setArray($this->innerLink->toFullPath(StoreRouted::getPath()->getPath()));
        $this->currentFile = $this->arrPath->getFileName();
        $this->mask = Config::get('Chapters', 'regexp_name', 'chapter_([0-9]{1,4})\.htm');

        $this->treeList
            ->setStartPath($this->arrPath->getArrayDirectory()) # use dir path
            ->setFilterCallback([$this, 'isUsable'])
            ->wantDeep(false)
            ->process()
        ;

        $this->availableFiles = $this->treeList->getRoot()->getSubNodes();
        sort($this->availableFiles);
        $this->position = $this->getPosition();
    }

    public function isUsable(Node $file): bool
    {
        $file = $this->arrPath->setArray($file->getPath())->getFileName();
        if ('.' == $file[0]) {
            return false;
        }

        if (preg_match('#' . $this->mask . '#ui', $file)) {
            return true;
        }

        return false;
    }

    protected function getPosition(): ?int
    {
        foreach ($this->availableFiles as $index => $file) {
            if ($this->currentFile == $file) {
                return (int) $index;
            }
        }
        return null;
    }

    /**
     * @throws PathsException
     * @return Output\AOutput
     */
    public function output(): Output\AOutput
    {
        $out = new Output\Html();
        if (is_null($this->position)) {
            return $out;
        }
        $content = '';
        if (isset($this->availableFiles[$this->position - 1])) {
            $content .= $this->tmplPage->reset()
                ->setTemplateName('prev_page')
                ->setData(
                    $this->position,
                    $this->linkExternal->linkVariant($this->arrPath->setArray($this->availableFiles[$this->position - 1]->getPath())->getString())
                )
                ->render();
        }
        $content .= $this->tmplPage->reset()
            ->setTemplateName('actual_page')
            ->setData(
                $this->position + 1,
                $this->linkExternal->linkVariant($this->arrPath->setArray($this->availableFiles[$this->position]->getPath())->getString())
            )
            ->render();
        if (isset($this->availableFiles[$this->position + 1])) {
            $content .= $this->tmplPage->reset()
                ->setTemplateName('next_page')
                ->setData(
                    $this->position + 2,
                    $this->linkExternal->linkVariant($this->arrPath->setArray($this->availableFiles[$this->position + 1]->getPath())->getString())
                )
                ->render();
        }
        return $out->setContent($content);
    }
}
