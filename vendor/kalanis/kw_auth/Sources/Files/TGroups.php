<?php

namespace kalanis\kw_auth\Sources\Files;


use kalanis\kw_auth\AuthException;
use kalanis\kw_auth\Data\FileGroup;
use kalanis\kw_auth\Interfaces\IAccessGroups;
use kalanis\kw_auth\Interfaces\IGroup;
use kalanis\kw_auth\Sources\TAuthLock;
use kalanis\kw_auth\Traits\TSeparated;
use kalanis\kw_locks\LockException;


/**
 * Trait TGroups
 * @package kalanis\kw_auth\Sources\Files
 * Work with groups
 */
trait TGroups
{
    use TAuthLock;
    use TSeparated;

    /**
     * @param IGroup $group
     * @throws AuthException
     * @throws LockException
     */
    public function createGroup(IGroup $group): void
    {
        $userId = $group->getGroupAuthorId();
        $groupName = $this->stripChars($group->getGroupName());
        $groupDesc = $this->stripChars($group->getGroupDesc());

        // not everything necessary is set
        if (empty($userId) || empty($groupName)) {
            throw new AuthException($this->getAuLang()->kauGroupMissParam());
        }
        $this->checkLock();

        $gid = 0;
        $this->getLock()->create();

        // read groups
        try {
            $groupLines = $this->openGroups();
        } catch (AuthException $ex) {
            // silence the problems on storage
            $groupLines = [];
        }
        foreach ($groupLines as &$line) {
            $gid = max($gid, $line[IAccessGroups::GRP_ID]);
        }
        $gid++;

        $newGroup = [
            IAccessGroups::GRP_ID => $gid,
            IAccessGroups::GRP_NAME => $groupName,
            IAccessGroups::GRP_AUTHOR => $userId,
            IAccessGroups::GRP_DESC => !empty($groupDesc) ? $groupDesc : $groupName,
            IAccessGroups::GRP_STATUS => $group->getGroupStatus(),
            IAccessGroups::GRP_PARENTS => $this->compactInt($group->getGroupParents()),
            IAccessGroups::GRP_FEED => '',
        ];
        ksort($newGroup);
        $groupLines[] = $newGroup;

        try {
            // now save it
            $this->saveGroups($groupLines);
        } finally {
            $this->getLock()->delete();
        }
    }

    /**
     * @param int $groupId
     * @throws AuthException
     * @throws LockException
     * @return IGroup|null
     */
    public function getGroupDataOnly(int $groupId): ?IGroup
    {
        $this->checkLock();
        try {
            $groupLines = $this->openGroups();
        } catch (AuthException $ex) {
            // silence the problems on storage
            return null;
        }
        foreach ($groupLines as &$line) {
            if ($line[IAccessGroups::GRP_ID] == $groupId) {
                return $this->getGroupClass($line);
            }
        }
        return null;
    }

    /**
     * @throws AuthException
     * @throws LockException
     * @return IGroup[]
     */
    public function readGroup(): array
    {
        $this->checkLock();

        $groupLines = $this->openGroups();
        $result = [];
        foreach ($groupLines as &$line) {
            $result[] = $this->getGroupClass($line);
        }

        return $result;
    }

    /**
     * @param array<int, string> $line
     * @return IGroup
     */
    protected function getGroupClass(array &$line): IGroup
    {
        $group = new FileGroup();
        $group->setData(
            intval($line[IAccessGroups::GRP_ID]),
            strval($line[IAccessGroups::GRP_NAME]),
            intval($line[IAccessGroups::GRP_AUTHOR]),
            strval($line[IAccessGroups::GRP_DESC]),
            intval($line[IAccessGroups::GRP_STATUS]),
            $this->separateInt($line[IAccessGroups::GRP_PARENTS])
        );
        return $group;
    }

    /**
     * @param IGroup $group
     * @throws AuthException
     * @throws LockException
     * @return bool
     */
    public function updateGroup(IGroup $group): bool
    {
        $groupName = $this->stripChars($group->getGroupName());
        $groupDesc = $this->stripChars($group->getGroupDesc());

        $this->checkLock();

        $this->getLock()->create();
        try {
            $groupLines = $this->openGroups();
        } finally {
            $this->getLock()->delete();
        }
        foreach ($groupLines as &$line) {
            if ($line[IAccessGroups::GRP_ID] == $group->getGroupId()) {
                // REFILL
                $line[IAccessGroups::GRP_NAME] = !empty($groupName) ? $groupName : $line[IAccessGroups::GRP_NAME] ;
                $line[IAccessGroups::GRP_DESC] = !empty($groupDesc) ? $groupDesc : $line[IAccessGroups::GRP_DESC] ;
                $line[IAccessGroups::GRP_STATUS] = $group->getGroupStatus();
                $line[IAccessGroups::GRP_PARENTS] = $this->compactInt($group->getGroupParents());
            }
        }

        try {
            $this->saveGroups($groupLines);
        } finally {
            $this->getLock()->delete();
        }
        return true;
    }

    /**
     * @param int $groupId
     * @throws AuthException
     * @throws LockException
     * @return bool
     */
    public function deleteGroup(int $groupId): bool
    {
        $this->checkLock();
        $this->checkRest($groupId);

        $changed = false;
        $this->getLock()->create();

        // update groups
        try {
            $openGroups = $this->openGroups();
        } catch (AuthException $ex) {
            // silence the problems on storage
            $this->getLock()->delete();
            return false;
        }
        foreach ($openGroups as $index => &$line) {
            if ($line[IAccessGroups::GRP_ID] == $groupId) {
                unset($openGroups[$index]);
                $changed = true;
            }
        }

        try {
            // now save it
            if ($changed) {
                $this->saveGroups($openGroups);
            }
        } finally {
            $this->getLock()->delete();
        }
        return true;
    }

    /**
     * Strip unwelcome characters from strings
     * @param string $input
     * @return string
     */
    abstract public function stripChars(string $input): string;

    /**
     * Check the rest of source for existence of group
     * @param int $groupId
     * @throws AuthException
     */
    abstract protected function checkRest(int $groupId): void;

    /**
     * @throws AuthException
     * @return array<int, array<int, string|int>>
     */
    abstract protected function openGroups(): array;

    /**
     * @param array<int, array<int, string|int>> $lines
     * @throws AuthException
     */
    abstract protected function saveGroups(array $lines): void;
}