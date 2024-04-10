<?php

namespace KWCMS\modules\Socials\Controllers;


use KWCMS\modules\Socials\Templates;


/**
 * Class TwitterAccount
 * @package KWCMS\modules\Socials\Controllers
 * Social page link twitter - account
 */
class TwitterAccount extends AAccount
{
    protected string $site = 'twitter';
    protected string $accountSite = 'twitter_account';
    protected string $shortSite = 'tw_account';

    protected function getTemplate(): Templates\ATmplAccount
    {
        return new Templates\TwAccount();
    }

    protected function fillLink(string $name): string
    {
        return '//twitter.com/' . $name;
    }
}
