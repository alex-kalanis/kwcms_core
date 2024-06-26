<?php

namespace KWCMS\modules\Chapters\Controllers;


use kalanis\kw_confs\ConfException;
use kalanis\kw_confs\Config;
use kalanis\kw_files\Access\Factory;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Node;
use kalanis\kw_modules\Output;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\PathsException;
use kalanis\kw_routed_paths\StoreRouted;
use kalanis\kw_templates\TemplateException;
use kalanis\kw_tree\DataSources\Files;
use kalanis\kw_tree\Essentials\FileNode;
use kalanis\kw_user_paths\InnerLinks;
use KWCMS\modules\Chapters\Lib;
use KWCMS\modules\Core\Libs\AModule;
use KWCMS\modules\Core\Libs\ExternalLink;
use KWCMS\modules\Core\Libs\FilesTranslations;


/**
 * Class Chapters
 * @package KWCMS\modules\Chapters\Controllers
 * Chapters as module
 */
class Chapters extends AModule
{
    protected ArrayPath $arrPath;
    protected Files $treeList;
    protected InnerLinks $innerLink;
    protected ExternalLink $linkExternal;
    protected Lib\PageTemplate $tmplPage;
    protected string $currentFile = '';
    protected string $mask = '';
    /** @var FileNode[] */
    protected array $availableFiles = [];
    protected ?int $position = null;

    /**
     * @param mixed ...$constructParams
     * @throws PathsException
     * @throws ConfException
     * @throws FilesException
     */
    public function __construct(...$constructParams)
    {
        Config::load('Chapters');
        $this->arrPath = new ArrayPath();
        $this->linkExternal = new ExternalLink(StoreRouted::getPath());
        $this->tmplPage = new Lib\PageTemplate();
        $this->innerLink = new InnerLinks(
            StoreRouted::getPath(),
            boolval(Config::get('Core', 'site.more_users', false)),
            boolval(Config::get('Core', 'page.more_lang', false)),
            [],
            boolval(Config::get('Core', 'page.system_prefix', false)),
            boolval(Config::get('Core', 'page.data_separator', false))
        );
        $this->treeList = new Files((new Factory(new FilesTranslations()))->getClass($constructParams));
    }

    /**
     * @throws PathsException
     */
    public function process(): void
    {
        $this->arrPath->setArray($this->innerLink->toFullPath([]));
        $this->currentFile = $this->arrPath->getFileName();
        $this->mask = Config::get('Chapters', 'regexp_name', 'chapter_([0-9]{1,4})\.htm');

//print_r(['proc', $this->arrPath]);
        $this->treeList
            ->setStartPath($this->arrPath->getArrayDirectory()) # use dir path
            ->setFilterCallback([$this, 'isUsable'])
            ->wantDeep(false)
            ->process()
        ;

        if ($this->treeList->getRoot()) {
            $this->availableFiles = $this->treeList->getRoot()->getSubNodes();
            sort($this->availableFiles);
            $this->position = $this->getPosition();
        }
    }

    public function isUsable(Node $file): bool
    {
        $file = $this->arrPath->setArray($file->getPath())->getFileName();
        if (empty($file)) {
            return false;
        }

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
     * @throws TemplateException
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
