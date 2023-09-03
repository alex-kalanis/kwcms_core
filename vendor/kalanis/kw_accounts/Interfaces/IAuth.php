<?php

namespace kalanis\kw_accounts\Interfaces;


use kalanis\kw_accounts\AccountsException;


/**
 * Interface IAuth
 * @package kalanis\kw_accounts\Interfaces
 * Authentication sources available on your system
 */
interface IAuth
{
    /**
     * Get data about chosen user
     * @param string $userName
     * @throws AccountsException
     * @return IUser|null
     */
    public function getDataOnly(string $userName): ?IUser;

    /**
     * Check if credentials are okay
     * @param string $userName
     * @param array<string|int, string|int|float|bool> $params
     * @throws AccountsException
     * @return IUser|null
     */
    public function authenticate(string $userName, array $params = []): ?IUser;
}
