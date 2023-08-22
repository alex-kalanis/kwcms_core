<?php

namespace kalanis\kw_modules\Interfaces;


use kalanis\kw_auth_sources\Interfaces\IUser;


/**
 * Class IModuleUser
 * @package kalanis\kw_modules\Interfaces
 * Module has user
 */
interface IModuleUser extends IModule
{
    /**
     * Return user
     * @return IUser|null
     */
    public function getUser(): ?IUser;
}
