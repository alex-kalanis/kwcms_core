<?php

namespace KWCMS\modules\Socials\Controllers;


use KWCMS\modules\Socials\Templates;


/**
 * Class InstagramAccount
 * @package KWCMS\modules\Socials\Controllers
 * Social page link Instagram - account
 */
class InstagramAccount extends AAccount
{
    protected $site = 'instagram';
    protected $accountSite = 'instagram_account';
    protected $shortSite = 'insta';

    protected function getTemplate(): Templates\ATmplAccount
    {
        return new Templates\InstaAccount();
    }

    protected function fillLink(string $name): string
    {
        return '//www.instagram.com/' . $name;
    }
}
