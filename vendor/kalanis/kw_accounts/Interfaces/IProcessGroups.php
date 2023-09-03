<?php

namespace kalanis\kw_accounts\Interfaces;


use kalanis\kw_accounts\AccountsException;


/**
 * Interface IProcessGroups
 * @package kalanis\kw_accounts\Interfaces
 * Work with groups
 * They are set against each user and say what is allowed to access
 */
interface IProcessGroups
{
    /**
     * @param IGroup $group
     * @throws AccountsException
     * @return bool
     */
    public function createGroup(IGroup $group): bool;

    /**
     * @param string $groupId
     * @throws AccountsException
     * @return IGroup|null
     */
    public function getGroupDataOnly(string $groupId): ?IGroup;

    /**
     * @throws AccountsException
     * @return IGroup[]
     */
    public function readGroup(): array;

    /**
     * @param IGroup $group
     * @throws AccountsException
     * @return bool
     */
    public function updateGroup(IGroup $group): bool;

    /**
     * @param string $groupId
     * @throws AccountsException
     * @return bool
     */
    public function deleteGroup(string $groupId): bool;
}
