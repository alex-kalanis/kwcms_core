<?php

namespace kalanis\kw_accounts\Interfaces;


use kalanis\kw_accounts\AccountsException;


/**
 * Interface IExpire
 * @package kalanis\kw_accounts\Interfaces
 * Authentication system expires at...
 */
interface IExpire
{
    /**
     * Set if authentication will expire in preset time
     * @param bool $expiry
     * @throws AccountsException
     */
    public function setExpireNotice(bool $expiry): void;

    /**
     * Return if authentication expire in following time
     * @throws AccountsException
     * @return bool
     */
    public function willExpire(): bool;

    /**
     * When it expire
     * @throws AccountsException
     * @return int
     */
    public function getExpireTime(): int;

    /**
     * Set time of expiration
     * @param int $expireTime
     * @throws AccountsException
     */
    public function updateExpireTime(int $expireTime): void;
}
