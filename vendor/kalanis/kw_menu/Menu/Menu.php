<?php

namespace kalanis\kw_menu\Menu;


/**
 * Class Menu
 * @package kalanis\kw_menu\Menu
 * Menu header
 */
class Menu
{
    protected string $file = '';
    protected string $name = '';
    protected string $title = '';
    protected int $displayCount = 0;
    /** @var Entry[] */
    protected array $entries = [];

    public function clear(): self
    {
        return $this->setData('', '', '', 0);
    }

    public function setData(string $file, string $name, string $title, int $count): self
    {
        $this->name = strval($name);
        $this->title = strval($title);
        $this->file = strval($file);
        $this->displayCount = intval($count);
        $this->entries = [];
        return $this;
    }

    public function addItem(Entry $entry): self
    {
        $this->entries[$entry->getId()] = $entry;
        return $this;
    }

    public function getName(): string
    {
        return strval($this->name);
    }

    public function getTitle(): string
    {
        return strval($this->title);
    }

    public function getFile(): string
    {
        return strval($this->file);
    }

    public function getDisplayCount(): int
    {
        return intval($this->displayCount);
    }

    /**
     * @return Entry[]
     */
    public function getEntries(): array
    {
        return $this->entries;
    }
}
