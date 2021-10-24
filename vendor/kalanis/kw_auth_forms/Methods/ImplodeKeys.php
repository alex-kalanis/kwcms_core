<?php

namespace kalanis\kw_auth_forms\Methods;


use kalanis\kw_auth\Interfaces\IUserCert;
use kalanis\kw_auth_forms\Interfaces\IMethod;


/**
 * Class ImplodeKeys
 * @package kalanis\kw_auth_forms\Methods
 * Check that inputs for verification
 */
class ImplodeKeys implements IMethod
{
    /** @var IUserCert|null */
    protected $keySource = null;
    /** @var string */
    protected $glue = '';

    public function __construct(IUserCert $keySource, string $glue = '|')
    {
        $this->keySource = $keySource;
        $this->glue = $glue;
    }

    public function check(string $got, $against): bool
    {
        $col = implode($this->glue, ((array)$against) + [$this->keySource->getPubSalt()]);
        return 1 === openssl_verify($col, $got, $this->keySource->getPubKey());
    }
}
