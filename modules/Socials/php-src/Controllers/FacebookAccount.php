<?php

namespace KWCMS\modules\Socials\Controllers;


use KWCMS\modules\Socials\Templates;


/**
 * Class FacebookAccount
 * @package KWCMS\modules\Socials\Controllers
 * Social page link facebook - account
 */
class FacebookAccount extends AAccount
{
    protected string $site = 'facebook';
    protected string $accountSite = 'facebook_account';
    protected string $shortSite = 'fb_account';

    protected function getTemplate(): Templates\ATmplAccount
    {
        return new Templates\FbAccount();
    }

    protected function fillLink(string $name): string
    {
        return '//facebook.com/' . $name;
    }
}
