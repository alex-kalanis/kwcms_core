<?php

namespace kalanis\kw_menu\Menu;


/**
 * Class Item
 * @package kalanis\kw_menu\Menu
 * Menu items
 */
class Item
{
    protected $name = '';
    protected $title = '';
    protected $file = '';
    protected $position = 0;
    protected $goSub = false;
    /** @var Menu|null */
    protected $submenu = null;

    public function setData(string $name, string $title, string $file, int $position, bool $goSub = false): self
    {
        $this->name = $name;
        $this->title = $title;
        $this->file = $file;
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getFile(): string
    {
        return $this->file;
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
