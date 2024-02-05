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
    protected $site = 'gitlab';
    protected $accountSite = 'gitlab_account';
    protected $shortSite = 'gl_account';

    protected function getTemplate(): Templates\ATmplAccount
    {
        return new Templates\GlAccount();
    }

    protected function fillLink(string $name): string
    {
        return '//gitlab.com/' . $name;
    }
}
