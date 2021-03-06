<?php

namespace KWCMS\modules\Admin;


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
        $method = $this->getAuthTree()->getMethod();
        if ($method) {
            $method->remove();
        }
        $this->user = null;
    }

    public function allowedAccessClasses(): array
    {
        return [IAccessClasses::CLASS_MAINTAINER, IAccessClasses::CLASS_ADMIN, IAccessClasses::CLASS_USER, ];
    }

    public function output(): Output\AOutput
    {
        return $this->result();
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
