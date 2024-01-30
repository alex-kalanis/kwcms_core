<?php

namespace kalanis\kw_accounts\Interfaces;


use kalanis\kw_accounts\AccountsException;


/**
 * Interface IAuthCert
 * @package kalanis\kw_accounts\Interfaces
 * Authentication sources available on your system - for certificates
 */
interface IAuthCert extends IAuth
{
    /**
     * Update certificate data in storage
     * @param string $userName
     * @param string|null $certKey
     * @param string|null $certSalt
     * @throws AccountsException
     * @return bool
     */
    public function updateCertData(string $userName, ?string $certKey, ?string $certSalt): bool;

    /**
     * Get certificate data from storage
     * @param string $userName
     * @throws AccountsException
     * @return ICert|null
     */
    public function getCertData(string $userName): ?ICert;
}
