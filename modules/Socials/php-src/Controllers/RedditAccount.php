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
    protected $site = 'reddit';
    protected $accountSite = 'reddit_account';
    protected $shortSite = 'rdt';

    protected function getTemplate(): Templates\ATmplAccount
    {
        return new Templates\RedditAccount();
    }

    protected function fillLink(string $name): string
    {
        return '//reddit.com/u/' . $name;
    }
}
