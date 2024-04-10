<?php

namespace KWCMS\modules\Socials\Controllers;


use KWCMS\modules\Socials\Templates;


/**
 * Class VkAccount
 * @package KWCMS\modules\Socials\Controllers
 * Social page link VKontakte - account
 */
class VkAccount extends AAccount
{
    protected string $site = 'vk';
    protected string $accountSite = 'vk_account';
    protected string $shortSite = 'vk';

    protected function getTemplate(): Templates\ATmplAccount
    {
        return new Templates\VkAccount();
    }

    protected function fillLink(string $name): string
    {
        return '//vk.com/' . $name;
    }
}
