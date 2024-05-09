<?php

namespace KWCMS\modules\Socials\Controllers;


use KWCMS\modules\Socials\Templates;


/**
 * Class PinterestAccount
 * @package KWCMS\modules\Socials\Controllers
 * Social page link twitter - account
 */
class PinterestAccount extends AAccount
{
    protected string $site = 'pinterest';
    protected string $accountSite = 'pinterest_account';
    protected string $shortSite = 'pin';

    protected function getTemplate(): Templates\ATmplAccount
    {
        return new Templates\PinAccount();
    }

    protected function fillLink(string $name): string
    {
        return '//pinterest.com/' . $name . '/';
    }
}
