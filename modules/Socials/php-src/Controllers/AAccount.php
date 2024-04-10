<?php

namespace KWCMS\modules\Socials\Controllers;


use kalanis\kw_modules\Output;
use KWCMS\modules\Core\Libs\AModule;
use KWCMS\modules\Socials\Templates\ATmplAccount;


/**
 * Class AAccount
 * @package KWCMS\modules\Socials\Controllers
 * Social page link - account
 * Abstract base
 */
abstract class AAccount extends AModule
{
    protected string $name = '';
    protected string $site = '';
    protected string $accountSite = '';
    protected string $shortSite = '';

    public function __construct(...$constructParams)
    {
        // from global configuration
        $account = isset($constructParams['socials']) && isset($constructParams['socials'][$this->site]) && isset($constructParams['socials'][$this->site]['account'])
            ? strval($constructParams['socials'][$this->site]['account']) : '';

        $account = empty($account) && isset($constructParams['socials']) && isset($constructParams['socials'][$this->accountSite])
            ? strval($constructParams['socials'][$this->accountSite]) : $account;

        $account = empty($account) && isset($constructParams['socials']) && isset($constructParams['socials'][$this->shortSite])
            ? strval($constructParams['socials'][$this->shortSite]) : $account;

        $account = empty($account) && isset($constructParams[$this->site]) && isset($constructParams[$this->site]['account'])
            ? strval($constructParams[$this->site]['account']) : $account;

        $account = empty($account) && isset($constructParams[$this->accountSite])
            ? strval($constructParams[$this->accountSite]) : $account;

        $account = empty($account) && isset($constructParams[$this->shortSite])
            ? strval($constructParams[$this->shortSite]) : $account;

        $this->name = $account;
    }

    public function process(): void
    {
        // from page
        $account = strval($this->getFromParam($this->accountSite, ''));
        $account = empty($account) ? strval($this->getFromParam('account', '')) : $account;
        $account = empty($account) ? strval($this->getFromParam('acc', '')) : $account;

        $this->name = empty($account) ? $this->name : $account;
    }

    public function output(): Output\AOutput
    {
        $out = new Output\Html();
        return $out->setContent(
            empty($this->name)
                ? ''
                : $this->getTemplate()->reset()->setData($this->fillLink(urlencode($this->name)))->render()
        );
    }

    abstract protected function getTemplate(): ATmplAccount;

    abstract protected function fillLink(string $name): string;
}
