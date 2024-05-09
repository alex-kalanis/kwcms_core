<?php

namespace KWCMS\modules\Socials\Controllers;


use KWCMS\modules\Socials\Templates;


/**
 * Class RedditAccount
 * @package KWCMS\modules\Socials\Controllers
 * Social page link Reddit - account
 */
class RedditAccount extends AAccount
{
    protected string $site = 'reddit';
    protected string $accountSite = 'reddit_account';
    protected string $shortSite = 'rdt';

    protected function getTemplate(): Templates\ATmplAccount
    {
        return new Templates\RedditAccount();
    }

    protected function fillLink(string $name): string
    {
        return '//reddit.com/u/' . $name;
    }
}
