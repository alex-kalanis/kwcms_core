<?php

namespace kalanis\kw_menu\Menu;


/**
 * Class Menu
 * @package kalanis\kw_menu\Menu
 * Menu header
 */
class Menu
{
    protected $file = '';
    protected $name = '';
    protected $title = '';
    protected $displayCount = 0;
    /** @var Entry[] */
    protected $entries = [];

    public function clear(): self
    {
        return $this->setData('', '', '', 0);
    }

    public function setData(string $file, string $name, string $title, int $count): self
    {
        $this->name = (string)$name;
        $this->title = (string)$title;
        $this->file = (string)$file;
        $this->displayCount = (string)$count;
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
        return (string)$this->name;
    }

    public function getTitle(): string
    {
        return (string)$this->title;
    }

    public function getFile(): string
    {
        return (string)$this->file;
    }

    public function getDisplayCount(): int
    {
        return (string)$this->displayCount;
    }

    /**
     * @return Entry[]
     */
    public function getEntries(): array
    {
        return $this->entries;
    }
}
