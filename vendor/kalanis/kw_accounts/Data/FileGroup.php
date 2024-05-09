<?php

namespace kalanis\kw_accounts\Data;


use kalanis\kw_accounts\Interfaces\IGroup;


/**
 * Class FileGroup
 * @package kalanis\kw_accounts\Data
 */
class FileGroup implements IGroup
{
    protected string $id = '0';
    protected string $name = '';
    protected string $author = '0';
    protected string $displayName = '';
    protected int $status = 0;
    /** @var string[] */
    protected array $parents = [];
    /** @var array<string|int, string|int|float|bool> */
    protected array $extra = [];

    public function setGroupData(?string $id, ?string $name, ?string $desc, ?string $authorId, ?int $status, ?array $parents = [], ?array $extra = []): void
    {
        $this->id = $id ?? $this->id;
        $this->name = $name ?? $this->name;
        $this->displayName = $desc ?? $this->displayName;
        $this->author = $authorId ?? $this->author;
        $this->status = $status ?? $this->status;
        $this->parents = $parents ?? $this->parents;
        $this->extra = !is_null($extra) ? array_merge($this->extra, $extra) : $this->extra;
    }

    public function getGroupId(): string
    {
        return $this->id;
    }

    public function getGroupName(): string
    {
        return $this->name;
    }

    public function getGroupAuthorId(): string
    {
        return $this->author;
    }

    public function getGroupDesc(): string
    {
        return $this->displayName;
    }

    public function getGroupStatus(): int
    {
        return $this->status;
    }

    /**
     * @return string[]
     */
    public function getGroupParents(): array
    {
        return $this->parents;
    }

    public function getGroupExtra(): array
    {
        return $this->extra;
    }
}
