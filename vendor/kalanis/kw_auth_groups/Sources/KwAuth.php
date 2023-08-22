<?php

namespace kalanis\kw_auth_groups\Sources;


use kalanis\kw_auth_sources\AuthSourcesException;
use kalanis\kw_auth_sources\Interfaces;
use kalanis\kw_groups\GroupsException;
use kalanis\kw_groups\Interfaces\ISource;
use kalanis\kw_locks\LockException;


/**
 * Class KwAuth
 * @package kalanis\kw_auth_groups\Sources
 * Process the menu against the file tree
 * Load more already unloaded entries and remove non-existing ones
 */
class KwAuth implements ISource
{
    /** @var Interfaces\IWorkGroups */
    protected $lib = null;

    public function __construct(Interfaces\IWorkGroups $lib)
    {
        $this->lib = $lib;
    }

    public function get(): array
    {
        try {
            $groups = $this->lib->readGroup();
            /** @var array<string, array<int, string>> $result */
            $result = [];
            foreach ($groups as $group) {
                $result[$group->getGroupId()] = $group->getGroupParents();
            }
            return $result;
        } catch (AuthSourcesException | LockException $ex) {
            throw new GroupsException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
