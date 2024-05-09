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
    protected string $site = 'github';
    protected string $accountSite = 'github_account';
    protected string $shortSite = 'gh_account';

    protected function getTemplate(): Templates\ATmplAccount
    {
        return new Templates\GhAccount();
    }

    protected function fillLink(string $name): string
    {
        return '//github.com/' . $name;
    }
}
