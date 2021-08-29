<?php

namespace KWCMS\modules\Admin;


use kalanis\kw_auth\Auth;
use kalanis\kw_auth\Interfaces\IAccessClasses;
use kalanis\kw_langs\Lang;
use kalanis\kw_modules\AAuthModule;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Output;


/**
 * Class Logout
 * @package KWCMS\modules\Admin
 * Admin Logout
 */
class Logout extends AAuthModule implements IModuleTitle
{
    protected $logoutTemplate = null;

    public function __construct()
    {
        Lang::load('Admin');
        $this->logoutTemplate = new Templates\LogoutTemplate();
    }

    protected function run(): void
    {
        $this->getAuthTree()->getMethod()->remove();
    }

    public function allowedAccessClasses(): array
    {
        return [IAccessClasses::CLASS_MAINTAINER, IAccessClasses::CLASS_ADMIN, IAccessClasses::CLASS_USER, ];
    }

    protected function result(): Output\AOutput
    {
        $out = new Output\Html();
        return $out->setContent($this->logoutTemplate->render());
    }

    public function getTitle(): string
    {
        return Lang::get('logout.page');
    }
}
