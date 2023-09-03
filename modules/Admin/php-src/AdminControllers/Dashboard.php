<?php

namespace KWCMS\modules\Admin\AdminControllers;


use kalanis\kw_accounts\Interfaces\IProcessClasses;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_modules\Output;
use KWCMS\modules\Core\Interfaces\Modules\IHasTitle;
use KWCMS\modules\Core\Libs\AAuthModule;


/**
 * Class Dashboard
 * @package KWCMS\modules\Admin\AdminControllers
 * Admin dashboard
 */
class Dashboard extends AAuthModule implements IHasTitle
{
    /**
     * @param mixed ...$constructParams
     * @throws LangException
     */
    public function __construct(...$constructParams)
    {
        Lang::load('Admin');
    }

    public function run(): void
    {
    }

    public function allowedAccessClasses(): array
    {
        return [IProcessClasses::CLASS_MAINTAINER, IProcessClasses::CLASS_ADMIN, IProcessClasses::CLASS_USER, ];
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
