<?php

namespace kalanis\kw_menu\Menu;


/**
 * Class Entry
 * @package kalanis\kw_menu\Menu
 * Menu entries
 */
class Entry
{
    protected string $id = '';
    protected string $name = '';
    protected string $desc = '';
    protected int $position = 0;
    protected bool $goSub = false;
    protected ?Menu $submenu = null;

    public function setData(string $id, string $name, string $desc, int $position, bool $goSub = false): self
    {
        $this->id = $id;
        $this->name = $name;
        $this->desc = $desc;
        $this->position = $position;
        $this->goSub = $goSub;
        $this->submenu = null;
        return $this;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;
        return $this;
    }

    public function addSubmenu(?Menu $menu): self
    {
        $this->submenu = $menu;
        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDesc(): string
    {
        return $this->desc;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function canGoSub(): bool
    {
        return $this->goSub;
    }

    public function getSubmenu(): ?Menu
    {
        return $this->submenu;
    }
}
