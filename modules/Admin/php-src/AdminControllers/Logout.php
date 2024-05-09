<?php

namespace KWCMS\modules\Admin\AdminControllers;


use kalanis\kw_accounts\Interfaces\IProcessClasses;
use kalanis\kw_auth\AuthException;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_modules\Output;
use KWCMS\modules\Admin\Templates;
use KWCMS\modules\Core\Interfaces\Modules\IHasTitle;
use KWCMS\modules\Core\Libs\AAuthModule;


/**
 * Class Logout
 * @package KWCMS\modules\Admin\AdminControllers
 * Admin Logout
 */
class Logout extends AAuthModule implements IHasTitle
{
    protected Templates\LogoutTemplate $logoutTemplate;

    /**
     * @param mixed ...$constructParams
     * @throws LangException
     */
    public function __construct(...$constructParams)
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
        return [IProcessClasses::CLASS_MAINTAINER, IProcessClasses::CLASS_ADMIN, IProcessClasses::CLASS_USER, ];
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
