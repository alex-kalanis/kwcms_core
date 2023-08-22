<?php

namespace KWCMS\modules\Admin\AdminControllers;


use kalanis\kw_auth\AuthException;
use kalanis\kw_auth_sources\Interfaces\IWorkClasses;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_modules\AAuthModule;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Output;
use KWCMS\modules\Admin\Templates;


/**
 * Class Logout
 * @package KWCMS\modules\Admin\AdminControllers
 * Admin Logout
 */
class Logout extends AAuthModule implements IModuleTitle
{
    protected $logoutTemplate = null;

    /**
     * @throws LangException
     */
    public function __construct()
    {
        Lang::load('Admin');
        $this->logoutTemplate = new Templates\LogoutTemplate();
    }

    /**
     * @throws AuthException
     */
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
        return [IWorkClasses::CLASS_MAINTAINER, IWorkClasses::CLASS_ADMIN, IWorkClasses::CLASS_USER, ];
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
