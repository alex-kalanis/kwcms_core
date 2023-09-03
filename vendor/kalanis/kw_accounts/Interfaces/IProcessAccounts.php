<?php

namespace kalanis\kw_accounts\Interfaces;


use kalanis\kw_accounts\AccountsException;


/**
 * Interface IProcessAccounts
 * @package kalanis\kw_accounts\Interfaces
 * Work with accounts
 */
interface IProcessAccounts
{
    /**
     * @param IUser $user
     * @param string $password
     * @throws AccountsException
     * @return bool
     */
    public function createAccount(IUser $user, string $password): bool;

    /**
     * @throws AccountsException
     * @return IUser[]
     */
    public function readAccounts(): array;

    /**
     * @param IUser $user
     * @throws AccountsException
     * @return bool
     */
    public function updateAccount(IUser $user): bool;

    /**
     * @param string $userName
     * @param string $passWord
     * @throws AccountsException
     * @return bool
     */
    public function updatePassword(string $userName, string $passWord): bool;

    /**
     * @param string $userName
     * @throws AccountsException
     * @return bool
     */
    public function deleteAccount(string $userName): bool;
}
