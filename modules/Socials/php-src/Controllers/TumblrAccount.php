<?php

namespace KWCMS\modules\Socials\Controllers;


use KWCMS\modules\Socials\Templates;


/**
 * Class TumblrAccount
 * @package KWCMS\modules\Socials\Controllers
 * Social page link Tumblr - blog account
 */
class TumblrAccount extends AAccount
{
    protected string $site = 'tumblr';
    protected string $accountSite = 'tumblr_account';
    protected string $shortSite = 'tmb_account';

    protected function getTemplate(): Templates\ATmplAccount
    {
        return new Templates\TmbAccount();
    }

    protected function fillLink(string $name): string
    {
        return '//www.tumblr.com/' . $name;
    }
}
