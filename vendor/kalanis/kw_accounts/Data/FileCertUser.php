<?php

namespace kalanis\kw_accounts\Data;


use kalanis\kw_accounts\Interfaces\IUserCert;


/**
 * Class FileCertUser
 * @package kalanis\kw_accounts\Data
 */
class FileCertUser extends FileUser implements IUserCert
{
    use TCerts;
}
