<?php

namespace KWCMS\modules\Langs\Controllers;


use kalanis\kw_confs\ConfException;
use kalanis\kw_confs\Config;
use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_files\Access\Factory;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Node;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_modules\AModule;
use kalanis\kw_modules\Interfaces\ISitePart;
use kalanis\kw_modules\Linking\ExternalLink;
use kalanis\kw_modules\Output\AOutput;
use kalanis\kw_modules\Output\Html;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stored;
use kalanis\kw_routed_paths\StoreRouted;
use kalanis\kw_tree\DataSources\Files;
use kalanis\kw_tree\Traits\TFilesDirs;
use kalanis\kw_user_paths\InnerLinks;
use KWCMS\modules\Core\Libs\FilesTranslations;
use KWCMS\modules\Langs\Templates;


/**
 * Class Langs
 * @package KWCMS\modules\Langs\Controllers
 * Site langs
 */
class Langs extends AModule
{
    use TFilesDirs;

    /** @var array<string, array<string, string>> */
    protected $possibleLangs = [];
    /** @var Node[] */
    protected $availableLangs = [];
    /** @var ExternalLink */
    protected $extLink = null;
    /** @var ArrayPath */
    protected $arrPath = null;
    /** @var CompositeAdapter */
    protected $files = null;
    /** @var Files */
    protected $treeList = null;
    /** @var InnerLinks */
    protected $innerLink = null;

    /**
     * @throws ConfException
     * @throws FilesException
     * @throws LangException
     * @throws PathsException
     */
    public function __construct()
    {
        Config::load(static::getClassName(static::class));
        Lang::load(static::getClassName(static::class));
        $this->extLink = new ExternalLink(Stored::getPath(), StoreRouted::getPath());
        $this->arrPath = new ArrayPath();
        $this->innerLink = new InnerLinks(
            StoreRouted::getPath(),
            boolval(Config::get('Core', 'site.more_users', false)),
            false // HERE MUST BE FALSE!!!
        );
        $this->files = (new Factory(new FilesTranslations()))->getClass(
            Stored::getPath()->getDocumentRoot() . Stored::getPath()->getPathToSystemRoot()
        );
        $this->treeList = new Files($this->files);
    }

    /**
     * @throws FilesException
     * @throws LangException
     * @throws PathsException
     */
    public function process(): void
    {
        if (!boolval(Config::get('Core', 'page.more_lang', false))) {
            return;
        }
        $this->possibleLangs = Lang::getLoader()->load(static::getClassName(static::class), 'dummy');
        $this->availableLangs = $this->getLangs();
    }

    /**
     * @throws FilesException
     * @throws PathsException
     * @return Node[]
     */
    protected function getLangs(): array
    {
        // get all langs from user
        $this->treeList->wantDeep(false);
        $this->treeList->setStartPath($this->innerLink->toFullPath([]));
        $this->treeList->setFilterCallback([$this, 'justDirsCallback']);
        $this->treeList->process();
        // which langs are available by both code and user
        $result = [];
        foreach ($this->treeList->getRoot()->getSubNodes() as $node) {
            $name = $this->arrPath->setArray($node->getPath())->getFileName();
            if (
                $this->files->exists($node->getPath())
                && $this->files->isDir($node->getPath())
                && (isset($this->possibleLangs[$name]))
            ) {
                $result[] = $node;
            }
        }
        return $result;
    }

    public function output(): AOutput
    {
        if (2 > count($this->availableLangs)) {
            return new Html();
        }
        return ($this->params[ISitePart::KEY_LEVEL] == ISitePart::SITE_LAYOUT) ? $this->outLayout() : $this->outContent() ;
    }

    protected function outLayout(): AOutput
    {
        $inputs = [];
        $tmplItem = new Templates\Item();
        foreach ($this->availableLangs as $node) {
            $lang = $this->arrPath->setArray($node->getPath())->getFileName();
            $inputs[] = $tmplItem->reset()->setData(
                $this->extLink->linkVariant(StoreRouted::getPath()->getPath(), '', false, $lang),
                $this->possibleLangs[$lang]['name'],
                $this->extLink->linkVariant('images/flags/' . $lang . '.png', 'sysimage', true, false),
                strval($this->getFromParam('vsize', '')),
                strval($this->getFromParam('hsize', ''))
            )->render();
        }

        $out = new Html();
        $tmpl = new Templates\Menu();
        return $out->setContent($tmpl->setData(implode('', $inputs))->render());
    }

    protected function outContent(): AOutput
    {
        $hints = [];
        $inputs = [];
        $tmplDesc = new Templates\Desc();
        $tmplLang = new Templates\Langs();
        foreach ($this->availableLangs as $node) {
            $lang = $this->arrPath->setArray($node->getPath())->getFileName();
            $hints[] = $tmplDesc->reset()->setData($this->possibleLangs[$lang]['hint'])->render();
            $inputs[] = $tmplLang->reset()->setData(
                $this->extLink->linkVariant('', '', false, $lang),
                $this->possibleLangs[$lang]['name'],
                $this->extLink->linkVariant('images/flags/' . $lang . '.png', 'sysimage', true, false),
                strval($this->getFromParam('vsize', '')),
                strval($this->getFromParam('hsize', ''))
            )->render();
        }

        $out = new Html();
        return $out->setContent(implode('', $hints) . implode('', $inputs));
    }
}
