<?php

namespace KWCMS\modules\Socials\Controllers;


use KWCMS\modules\Socials\Templates;


/**
 * Class TwitchAccount
 * @package KWCMS\modules\Socials\Controllers
 * Social page link Twitch - account
 */
class TwitchAccount extends AAccount
{
    protected string $site = 'twitch';
    protected string $accountSite = 'twitch_account';
    protected string $shortSite = 'tt_account';

    protected function getTemplate(): Templates\ATmplAccount
    {
        return new Templates\TtAccount();
    }

    protected function fillLink(string $name): string
    {
        return '//twitch.tv/' . $name;
    }
}
