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
use kalanis\kw_modules\Interfaces\Lists\ISitePart;
use kalanis\kw_modules\Output\AOutput;
use kalanis\kw_modules\Output\Html;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\PathsException;
use kalanis\kw_routed_paths\StoreRouted;
use kalanis\kw_tree\DataSources\Files;
use kalanis\kw_tree\Traits\TFilesDirs;
use kalanis\kw_user_paths\UserInnerLinks;
use KWCMS\modules\Core\Libs\AModule;
use KWCMS\modules\Core\Libs\ExternalLink;
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
    protected array $possibleLangs = [];
    /** @var Node[] */
    protected array $availableLangs = [];
    protected ExternalLink $extLink;
    protected ArrayPath $arrPath;
    protected CompositeAdapter $files;
    protected Files $treeList;
    protected UserInnerLinks $innerLink;
    protected bool $willShow = false;

    /**
     * @param mixed ...$constructParams
     * @throws ConfException
     * @throws FilesException
     * @throws LangException
     * @throws PathsException
     */
    public function __construct(...$constructParams)
    {
        Config::load(static::getClassName(static::class));
        Lang::load(static::getClassName(static::class));
        $this->extLink = new ExternalLink(StoreRouted::getPath());
        $this->arrPath = new ArrayPath();
        $this->innerLink = new UserInnerLinks(strval(Config::get('Core', 'page.default_user', isset($constructParams['default_user'])
                ? $constructParams['default_user']
                : '/user/')
            )
        );
        $this->files = (new Factory(new FilesTranslations()))->getClass($constructParams);
        $this->treeList = new Files($this->files);
        $this->willShow = boolval(Config::get('Core', 'page.more_lang', false));
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
        if (!$this->willShow) {
            return;
        }
        $this->possibleLangs = Lang::getLoader()->load('Langs', 'dummy');
        $this->availableLangs = $this->getLangs();
    }

    /**
     * @throws FilesException
     * @throws PathsException
     * @return Node[]
     */
    protected function getLangs(): array
    {
        $startPath = $this->innerLink->toFullPath([]);
        // get all langs from user
        $this->treeList->wantDeep(false);
        $this->treeList->setStartPath($startPath);
        $this->treeList->setFilterCallback([$this, 'justDirsCallback']);
        $this->treeList->process();
        // which langs are available by both code and user
        $result = [];
        foreach ($this->treeList->getRoot()->getSubNodes() as $node) {
            $name = $this->arrPath->setArray($node->getPath())->getFileName();
            if (
                $this->files->exists($startPath + $node->getPath())
                && $this->files->isDir($startPath + $node->getPath())
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

    /**
     * @throws PathsException
     * @return AOutput
     */
    protected function outLayout(): AOutput
    {
        $inputs = [];
        $tmplItem = new Templates\Item();
        foreach ($this->availableLangs as $node) {
            $lang = $this->arrPath->setArray($node->getPath())->getFileName();
            $noLangPath = StoreRouted::getPath()->getPath();
            array_shift($noLangPath);
            $inputs[] = $tmplItem->reset()->setData(
                $this->extLink->linkVariant($noLangPath, [], false, $lang),
                $this->possibleLangs[$lang]['name'],
                $this->extLink->linkVariant('images/flags/' . $lang . '.png', 'sysimage', true),
                strval($this->getFromParam('vsize', '20')),
                strval($this->getFromParam('hsize', '20'))
            )->render();
        }

        $out = new Html();
        $tmpl = new Templates\Menu();
        return $out->setContent($tmpl->setData(implode('', $inputs))->render());
    }

    /**
     * @throws PathsException
     * @return AOutput
     */
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
                $this->extLink->linkVariant($lang),
                $this->possibleLangs[$lang]['name'],
                $this->extLink->linkVariant('images/flags/' . $lang . '.png', 'sysimage', true),
                strval($this->getFromParam('vsize', '')),
                strval($this->getFromParam('hsize', ''))
            )->render();
        }

        $out = new Html();
        return $out->setContent(implode('', $hints) . implode('', $inputs));
    }
}
