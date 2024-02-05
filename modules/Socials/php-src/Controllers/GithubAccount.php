<?php

namespace KWCMS\modules\Socials\Controllers;


use KWCMS\modules\Socials\Templates;


/**
 * Class GithubAccount
 * @package KWCMS\modules\Socials\Controllers
 * Social page link Github - account
 */
class GithubAccount extends AAccount
{
    protected $site = 'github';
    protected $accountSite = 'github_account';
    protected $shortSite = 'gh_account';

    protected function getTemplate(): Templates\ATmplAccount
    {
        return new Templates\GhAccount();
    }

    protected function fillLink(string $name): string
    {
        return '//github.com/' . $name;
    }
}
