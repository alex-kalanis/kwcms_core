<?php

namespace kalanis\kw_auth_forms\Methods;


use kalanis\kw_auth\Interfaces\IUserCert;
use kalanis\kw_auth_forms\Interfaces\IMethod;


/**
 * Class ImplodeHash
 * @package kalanis\kw_auth_forms\Methods
 * Check that inputs for hash sum if equals for imploded string
 */
class ImplodeHash implements IMethod
{
    /** @var IUserCert|null */
    protected $keySource = null;
    /** @var string */
    protected $glue = '';
    /** @var string */
    protected $algorithm = '';

    public function __construct(IUserCert $keySource, string $glue = '|', string $algorithm = 'md5')
    {
        $this->keySource = $keySource;
        $this->glue = $glue;
        $this->algorithm = $algorithm;
    }

    public function check(string $got, $against): bool
    {
        $col = implode($this->glue, ((array)$against) + [$this->keySource->getPubSalt()]);
        return hash($this->algorithm, $col) == $got;
    }
}
