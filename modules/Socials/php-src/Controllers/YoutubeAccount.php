<?php

namespace KWCMS\modules\Socials\Controllers;


use KWCMS\modules\Socials\Templates;


/**
 * Class YoutubeAccount
 * @package KWCMS\modules\Socials\Controllers
 * Social page link twitter - account
 */
class YoutubeAccount extends AAccount
{
    protected string $site = 'youtube';
    protected string $accountSite = 'youtube_account';
    protected string $shortSite = 'yt_account';

    protected function getTemplate(): Templates\ATmplAccount
    {
        return new Templates\YtAccount();
    }

    protected function fillLink(string $name): string
    {
        return '//www.youtube.com/' . $name;
    }
}
