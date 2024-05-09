<?php

namespace KWCMS\modules\Socials\Controllers;


use KWCMS\modules\Socials\Templates;


/**
 * Class GitlabAccount
 * @package KWCMS\modules\Socials\Controllers
 * Social page link Github - account
 */
class GitlabAccount extends AAccount
{
    protected string $site = 'gitlab';
    protected string $accountSite = 'gitlab_account';
    protected string $shortSite = 'gl_account';

    protected function getTemplate(): Templates\ATmplAccount
    {
        return new Templates\GlAccount();
    }

    protected function fillLink(string $name): string
    {
        return '//gitlab.com/' . $name;
    }
}
