<?php

namespace kalanis\kw_accounts\Data;


/**
 * Trait TCerts
 * @package kalanis\kw_accounts\Data
 * Work with certificates
 */
trait TCerts
{
    /** @var string */
    protected $pubKey = '';
    /** @var string */
    protected $salt = '';

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
