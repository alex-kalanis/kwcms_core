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
    protected $site = 'facebook';
    protected $accountSite = 'facebook_account';
    protected $shortSite = 'fb_account';

    protected function getTemplate(): Templates\ATmplAccount
    {
        return new Templates\FbAccount();
    }

    protected function fillLink(string $name): string
    {
        return '//facebook.com/' . $name;
    }
}
