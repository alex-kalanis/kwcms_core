<?php

namespace KWCMS\modules\Core\Interfaces\Modules;


use kalanis\kw_accounts\Interfaces\IUser;
use kalanis\kw_modules\Interfaces\IModule;


/**
 * Class IHasUser
 * @package KWCMS\modules\Core\Interfaces\Modules
 * Module has user
 */
interface IHasUser extends IModule
{
    /**
     * Return user
     * @return IUser|null
     */
    public function getUser(): ?IUser;
}
