<?php

namespace kalanis\kw_accounts\Data;


use kalanis\kw_accounts\Interfaces\IUser;
use kalanis\kw_accounts\Interfaces\IProcessClasses;


/**
 * Class FileUser
 * @package kalanis\kw_accounts\Data
 */
class FileUser implements IUser
{
    protected string $authId = '0';
    protected string $authName = '';
    protected string $authGroup = '0';
    protected int $authClass = IProcessClasses::CLASS_UNKNOWN;
    protected int $authStatus = IUser::USER_STATUS_UNKNOWN;
    protected string $displayName = '';
    protected string $dir = '';
    /** @var array<string|int, string|int|float|bool> */
    protected array $extra = [];

    public function setUserData(?string $authId, ?string $authName, ?string $authGroup, ?int $authClass, ?int $authStatus, ?string $displayName, ?string $dir, ?array $extra = []): void
    {
        $this->authId = $authId ?? $this->authId;
        $this->authName = $authName ?? $this->authName;
        $this->authGroup = $authGroup ?? $this->authGroup;
        $this->authClass = $authClass ?? $this->authClass;
        $this->authStatus = $authStatus ?? $this->authStatus;
        $this->displayName = $displayName ?? $this->displayName;
        $this->dir = $dir ?? $this->dir;
        $this->extra = !is_null($extra) ? array_merge($this->extra, $extra) : $this->extra;
    }

    public function getAuthId(): string
    {
        return $this->authId;
    }

    public function getAuthName(): string
    {
        return $this->authName;
    }

    public function getGroup(): string
    {
        return $this->authGroup;
    }

    public function getClass(): int
    {
        return $this->authClass;
    }

    public function getStatus(): int
    {
        return $this->authStatus;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function getDir(): string
    {
        return $this->dir;
    }

    public function getExtra(): array
    {
        return $this->extra;
    }
}
