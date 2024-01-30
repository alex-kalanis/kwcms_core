<?php

namespace kalanis\kw_accounts\Interfaces;


/**
 * Interface ICert
 * @package kalanis\kw_accounts\Interfaces
 * Certificates data from your auth system
 */
interface ICert
{
    /**
     * Fill certificates; null values will not change
     * @param string $pubKey
     * @param string $salt
     */
    public function updateCertInfo(?string $pubKey, ?string $salt): void;

    /**
     * Public salt or password to certificate or other things which uses this interface
     * @return string
     */
    public function getSalt(): string;

    /**
     * Certificate or other key itself
     * @return string
     */
    public function getPubKey(): string;
}
