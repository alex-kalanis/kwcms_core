<?php

namespace kalanis\kw_auth_forms\Interfaces;


use kalanis\kw_rules\Exceptions\RuleException;


/**
 * Interface IMethod
 * @package kalanis\kw_auth_forms\Interfaces
 * For checking content from obtained form input and other ones
 */
interface IMethod
{
    /**
     * @param string $got
     * @param $against
     * @return bool
     * @throws RuleException
     */
    public function check(string $got, $against): bool;
}
