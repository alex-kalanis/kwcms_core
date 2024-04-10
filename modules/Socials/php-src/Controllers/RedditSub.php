<?php

namespace KWCMS\modules\Socials\Controllers;


use kalanis\kw_modules\Output;
use KWCMS\modules\Core\Libs\AModule;
use KWCMS\modules\Socials\Templates;


/**
 * Class RedditSub
 * @package KWCMS\modules\Socials\Controllers
 * Social page link - Reddit Sub
 */
class RedditSub extends AModule
{
    protected string $name = '';
    protected string $site = 'reddit';
    protected string $accountSite = 'reddit_sub';
    protected string $shortSite = 'rdt';

    public function __construct(...$constructParams)
    {
        // from global configuration
        $sub = isset($constructParams['socials']) && isset($constructParams['socials'][$this->site]) && isset($constructParams['socials'][$this->site]['sub'])
            ? strval($constructParams['socials'][$this->site]['sub']) : '';

        $sub = empty($sub) && isset($constructParams['socials']) && isset($constructParams['socials'][$this->accountSite])
            ? strval($constructParams['socials'][$this->accountSite]) : $sub;

        $sub = empty($sub) && isset($constructParams['socials']) && isset($constructParams['socials'][$this->shortSite])
            ? strval($constructParams['socials'][$this->shortSite]) : $sub;

        $sub = empty($sub) && isset($constructParams[$this->site]) && isset($constructParams[$this->site]['sub'])
            ? strval($constructParams[$this->site]['sub']) : $sub;

        $sub = empty($sub) && isset($constructParams[$this->accountSite])
            ? strval($constructParams[$this->accountSite]) : $sub;

        $sub = empty($sub) && isset($constructParams[$this->shortSite])
            ? strval($constructParams[$this->shortSite]) : $sub;

        $this->name = $sub;
    }

    public function process(): void
    {
        // from page
        $sub = strval($this->getFromParam($this->accountSite, ''));
        $sub = empty($sub) ? strval($this->getFromParam('sub', '')) : $sub;

        $this->name = empty($sub) ? $this->name : $sub;
    }

    public function output(): Output\AOutput
    {
        $out = new Output\Html();
        return $out->setContent(
            empty($this->name)
                ? ''
                : $this->getTemplate()->reset()->setData($this->fillLink($this->name))->render()
        );
    }

    protected function getTemplate(): Templates\ATmplAccount
    {
        return new Templates\RedditSub();
    }

    protected function fillLink(string $name): string
    {
        return '//reddit.com/r/' . $name;
    }
}
