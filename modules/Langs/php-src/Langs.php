<?php

namespace KWCMS\modules\Langs;


use kalanis\kw_confs\Config;
use kalanis\kw_langs\Lang;
use kalanis\kw_modules\AModule;
use kalanis\kw_modules\Interfaces\ISitePart;
use kalanis\kw_modules\Linking\ExternalLink;
use kalanis\kw_modules\Linking\InternalLink;
use kalanis\kw_modules\Output\AOutput;
use kalanis\kw_modules\Output\Html;
use kalanis\kw_paths\Stored;


/**
 * Class Langs
 * @package KWCMS\modules\Langs
 * Site langs
 */
class Langs extends AModule
{
    protected $possibleLangs = [];
    protected $availableLangs = [];
    protected $extLink = null;
    protected $intLink = null;

    public function __construct()
    {
        Config::load(static::getClassName(static::class));
        Lang::load(static::getClassName(static::class));
        $this->extLink = new ExternalLink(Stored::getPath());
        $this->intLink = new InternalLink(Stored::getPath());
    }

    public function process(): void
    {
        if (!boolval(Config::get('Core', 'page.more_lang', false))) {
            return;
        }
        $this->possibleLangs = Lang::getLoader()->load(static::getClassName(static::class), 'dummy');
        $this->availableLangs = $this->getLangs();
    }

    protected function getLangs(): array
    {
        $result = [];
        # which langs are available by code
        # get all langs from user
        $userDir = $this->intLink->userContent('', false);
        foreach (scandir($userDir) as $item) {
            $nameLen = mb_strlen($item);
            if ( is_dir($userDir . DIRECTORY_SEPARATOR . $item) && (isset($this->possibleLangs[$nameLen])) ) {
                $result[] = $item;
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
        foreach ($this->availableLangs as $lang) {
            $inputs[] = $tmplItem->reset()->setData(
                $this->outLink($lang),
                $this->possibleLangs[$lang]['name'],
                $this->extLink->linkModule('sysimage', 'images/flags/' . $lang . '.png'),
                (string)$this->getFromParam('vsize', ''),
                (string)$this->getFromParam('hsize', '')
            )->render();
        }

        $out = new Html();
        $tmpl = new Templates\Menu();
        return $out->setContent($tmpl->setData(implode('', $inputs))->render());
    }

    protected function outLink(string $lang): string
    {
        return $this->extLink->linkVariant('', '', false, $lang);
    }

    protected function outContent(): AOutput
    {
        $inputs = [];
        $tmplDesc = new Templates\Desc();
        foreach ($this->availableLangs as $lang) {
            $inputs[] = $tmplDesc->reset()->setData($this->possibleLangs[$lang]['hint'])->render();
        }
        $tmplLang = new Templates\Langs();
        foreach ($this->availableLangs as $lang) {
            $inputs[] = $tmplLang->reset()->setData(
                $this->startLink($lang),
                $this->possibleLangs[$lang]['name'],
                $this->extLink->linkModule('sysimage', 'images/flags/' . $lang . '.png'),
                (string)$this->getFromParam('vsize', ''),
                (string)$this->getFromParam('hsize', '')
            )->render();
        }

        $out = new Html();
        return $out->setContent(implode('', $inputs));
    }

    protected function startLink(string $lang): string
    {
        return $this->extLink->linkVariant('', '', false, $lang);
    }
}
