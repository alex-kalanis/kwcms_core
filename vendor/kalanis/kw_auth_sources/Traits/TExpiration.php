<?php

namespace kalanis\kw_auth_sources\Traits;


use kalanis\kw_auth_sources\AuthSourcesException;
use kalanis\kw_auth_sources\Interfaces\IExpire;


/**
 * Trait TExpiration
 * @package kalanis\kw_auth_sources\Traits
 * Expiration of password
 */
trait TExpiration
{
    /** @var int */
    protected $changeInterval = 31536000; // 60×60×24×365 - one year
    /** @var int */
    protected $changeNotice = 2592000; // 60×60×24×30 - one month

    protected function initExpiry(int $changeInterval, int $changeNoticeBefore): void
    {
        $this->changeInterval = $changeInterval;
        $this->changeNotice = $changeNoticeBefore;
    }

    /**
     * @param object|int|string $class
     * @param int $nextChange
     * @throws AuthSourcesException
     */
    public function setExpirationNotice($class, int $nextChange): void
    {
        if ($class && is_object($class) && $class instanceof IExpire) {
            $class->setExpireNotice( $this->shallExpire($nextChange));
        }
    }

    protected function shallExpire(int $nextChange): bool
    {
        return $this->getTime() + $this->changeNotice > $nextChange;
    }

    /**
     * @param object|int|string $class
     * @throws AuthSourcesException
     */
    public function updateExpirationTime($class): void
    {
        if ($class && is_object($class) && $class instanceof IExpire) {
            $class->updateExpireTime($this->whenItExpire());
        }
    }

    protected function whenItExpire(): int
    {
        return $this->getTime() + $this->changeInterval;
    }

    /**
     * @return int
     * @codeCoverageIgnore too dynamic
     */
    protected function getTime(): int
    {
        return time();
    }
}
