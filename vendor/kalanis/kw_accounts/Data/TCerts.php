<?php

namespace kalanis\kw_accounts\Data;


/**
 * Trait TCerts
 * @package kalanis\kw_accounts\Data
 * Work with certificates
 */
trait TCerts
{
    protected string $pubKey = '';
    protected string $salt = '';

    public function updateCertInfo(?string $pubKey, ?string $salt): void
    {
        $this->pubKey = $pubKey ?? $this->pubKey;
        $this->salt = $salt ?? $this->salt;
    }

    public function getPubKey(): string
    {
        return $this->pubKey;
    }

    public function getSalt(): string
    {
        return $this->salt;
    }
}
