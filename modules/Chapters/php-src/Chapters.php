<?php

namespace KWCMS\modules\Chapters;


use kalanis\kw_confs\Config;
use kalanis\kw_listing\DirectoryListing;
use kalanis\kw_modules\AModule;
use kalanis\kw_modules\ExternalLink;
use kalanis\kw_modules\InternalLink;
use kalanis\kw_modules\Output;
use kalanis\kw_paths\Stuff;


/**
 * Class Chapters
 * @package KWCMS\modules\Contact
 * Chapters as module
 */
class Chapters extends AModule
{
    /** @var DirectoryListing|null */
    protected $dirList = null;
    /** @var InternalLink */
    protected $linkInternal = null;
    /** @var ExternalLink */
    protected $linkExternal = null;
    /** @var Lib\PageTemplate */
    protected $tmplPage = null;

    protected $dir = '';
    protected $file = '';
    protected $path = '';
    protected $mask = '';
    protected $availableFiles = [];
    protected $position = null;

    public function __construct()
    {
        Config::load('Chapters');
        $this->linkInternal = new InternalLink(Config::getPath());
        $this->linkExternal = new ExternalLink(Config::getPath());
        $this->tmplPage = new Lib\PageTemplate();
        $this->dirList = new DirectoryListing();
    }

    public function process(): void
    {
        $this->path = Config::getPath()->getPath();
        $fullPath = $this->linkInternal->userContent($this->path);
        $this->dir = Stuff::directory($fullPath);
        $this->file = Stuff::filename($fullPath);
        $this->mask = Config::get('Chapters', 'regexp_name', 'chapter_([0-9]{1,4})\.htm');

        $this->dirList
            ->setPath($this->dir) # use dir path
            ->setUsableCallback([$this, 'isUsable'])
            ->process()
        ;

        $this->availableFiles = $this->dirList->getFiles();
        sort($this->availableFiles);
        $this->position = $this->getPosition();
    }

    public function isUsable(string $file): bool
    {
        if ('.' == $file[0]) {
            return false;
        }

        if (!is_file($this->dir . DIRECTORY_SEPARATOR . $file)) {
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
            if ($this->file == $file) {
                return (int)$index;
            }
        }
        return null;
    }

    public function output(): Output\AOutput
    {
        $out = new Output\Html();
        if (is_null($this->position)) {
            return $out;
        }
        $content = '';
        $currentDir = Stuff::directory(Config::getPath()->getPath());
        if (isset($this->availableFiles[$this->position - 1])) {
            $content .= $this->tmplPage->reset()
                ->setTemplateName('prev_page')
                ->setData(
                    $this->position,
                    $this->linkExternal->linkVariant($currentDir . $this->availableFiles[$this->position - 1] )
                )
                ->render();
        }
        $content .= $this->tmplPage->reset()
            ->setTemplateName('actual_page')
            ->setData(
                $this->position + 1,
                $this->linkExternal->linkVariant($currentDir . $this->availableFiles[$this->position] )
            )
            ->render();
        if (isset($this->availableFiles[$this->position + 1])) {
            $content .= $this->tmplPage->reset()
                ->setTemplateName('next_page')
                ->setData(
                    $this->position + 2,
                    $this->linkExternal->linkVariant($currentDir . $this->availableFiles[$this->position + 1] )
                )
                ->render();
        }
        return $out->setContent($content);
    }
}
