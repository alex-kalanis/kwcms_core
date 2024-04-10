<?php

namespace kalanis\kw_accounts\Interfaces;


/**
 * Interface IUser
 * @package kalanis\kw_accounts\Interfaces
 * User data from your auth system
 */
interface IUser
{
    public const LOWEST_USER_ID = '1000';
    public const STATUS_NONE = 'none';

    public const USER_STATUS_ERROR = 0;
    public const USER_STATUS_ENABLED = 1;
    public const USER_STATUS_UNKNOWN = 2;
    public const USER_STATUS_DISABLED = 3;
    public const USER_STATUS_ONLY_LOGIN = 4;
    public const USER_STATUS_ONLY_CERT = 5;

    /**
     * Fill user; null values will not change
     * @param string|null $authId
     * @param string|null $authName
     * @param string|null $authGroup
     * @param int|null $authClass
     * @param int|null $authStatus
     * @param string|null $displayName
     * @param string|null $dir
     * @param array<string|int, string|int|float|bool>|null $extra
     */
    public function setUserData(?string $authId, ?string $authName, ?string $authGroup, ?int $authClass, ?int $authStatus, ?string $displayName, ?string $dir, ?array $extra = []): void;

    /**
     * ID in system, usualy number, but can use string, so I pass string
     * @return string
     */
    public function getAuthId(): string;

    /**
     * Human-understandable name of account; usually this one is login
     * @return string
     */
    public function getAuthName(): string;

    /**
     * ID of group of account; similar rule as with user ID
     * @return string
     */
    public function getGroup(): string;

    /**
     * Class of user in system
     * @return int
     */
    public function getClass(): int;

    /**
     * Status of user in system - like enabled, limited access, ...
     * @return int
     */
    public function getStatus(): int;

    /**
     * What will be shown to others; can be the same with auth name
     * @return string
     */
    public function getDisplayName(): string;

    /**
     * Home directory of user on file-based storages like kwcms
     * @return string
     */
    public function getDir(): string;

    /**
     * Extra data about user
     * @return array<string|int, string|int|float|bool>
     */
    public function getExtra(): array;
}
