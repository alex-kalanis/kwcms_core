<?php

namespace KWCMS\modules\Admin;


use kalanis\kw_auth\Interfaces\IAccessClasses;
use kalanis\kw_langs\Lang;
use kalanis\kw_modules\AAuthModule;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Output\AOutput;


/**
 * Class Dashboard
 * @package KWCMS\modules\Admin
 * Admin dashboard
 */
class Dashboard extends AAuthModule implements IModuleTitle
{
    public function __construct()
    {
        Lang::load('Admin');
    }

    public function run(): void
    {
    }

    public function allowedAccessClasses(): array
    {
        return [IAccessClasses::CLASS_MAINTAINER, IAccessClasses::CLASS_ADMIN, IAccessClasses::CLASS_USER, ];
    }

    public function result(): AOutput
    {
        $out = new Shared\FillHtml($this->user);
        ob_start();
        var_dump($_SESSION);
        $out->setContent(ob_get_clean());
        return $out;
    }

    public function getTitle(): string
    {
        return Lang::get('dashboard.page');
    }
}
