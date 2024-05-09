<?php

namespace kalanis\kw_accounts\Interfaces;


/**
 * Interface IProcessClasses
 * @package kalanis\kw_accounts\Interfaces
 * Work with system classes
 * They are set against the system and tells what is allowed to run against the code
 * This is due necessity to leave groups out - the hardcoded group can exist or not -> that depends on system and admin.
 * This is set by code itself and did not depend on groups anymore.
 */
interface IProcessClasses
{
    public const CLASS_UNKNOWN = 0;
    public const CLASS_MAINTAINER = 1;
    public const CLASS_ADMIN = 2;
    public const CLASS_USER = 3;

    /**
     * @return array<int, string>
     */
    public function readClasses(): array;
}
