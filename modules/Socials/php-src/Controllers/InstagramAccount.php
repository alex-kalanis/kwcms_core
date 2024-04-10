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
    protected string $site = 'instagram';
    protected string $accountSite = 'instagram_account';
    protected string $shortSite = 'insta';

    protected function getTemplate(): Templates\ATmplAccount
    {
        return new Templates\InstaAccount();
    }

    protected function fillLink(string $name): string
    {
        return '//www.instagram.com/' . $name;
    }
}
