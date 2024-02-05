<?php

namespace KWCMS\modules\Socials\Controllers;


use KWCMS\modules\Socials\Templates;


/**
 * Class LinkedinAccount
 * @package KWCMS\modules\Socials\Controllers
 * Social page link Twitch - account
 */
class LinkedinAccount extends AAccount
{
    protected $site = 'linkedin';
    protected $accountSite = 'linkedin_account';
    protected $shortSite = 'in';

    protected function getTemplate(): Templates\ATmplAccount
    {
        return new Templates\InAccount();
    }

    protected function fillLink(string $name): string
    {
        return '//www.linkedin.com/in/' . $name . '/';
    }
}
