<?php

namespace KWCMS\modules\Admin\AdminControllers;


use kalanis\kw_auth_sources\Interfaces\IWorkClasses;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_modules\AAuthModule;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Output;


/**
 * Class Dashboard
 * @package KWCMS\modules\Admin\AdminControllers
 * Admin dashboard
 */
class Dashboard extends AAuthModule implements IModuleTitle
{
    /**
     * @throws LangException
     */
    public function __construct()
    {
        Lang::load('Admin');
    }

    public function run(): void
    {
    }

    public function allowedAccessClasses(): array
    {
        return [IWorkClasses::CLASS_MAINTAINER, IWorkClasses::CLASS_ADMIN, IWorkClasses::CLASS_USER, ];
    }

    public function result(): Output\AOutput
    {
        $out = new Output\Html();
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
