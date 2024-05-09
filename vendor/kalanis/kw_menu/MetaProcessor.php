<?php

namespace kalanis\kw_menu;


use kalanis\kw_menu\Interfaces\IMNTranslations;
use kalanis\kw_menu\Traits\TLang;


/**
 * Class MetaProcessor
 * @package kalanis\kw_menu
 * Menu data processor - CRUD
 */
class MetaProcessor
{
    use TLang;

    protected Interfaces\IMetaSource $metaSource;
    protected Menu\Menu $menu;
    protected Menu\Entry $entry;
    protected int $highest = 0;
    /** @var Menu\Entry[] */
    protected array $workList = [];

    public function __construct(Interfaces\IMetaSource $metaSource, ?IMNTranslations $lang = null)
    {
        $this->menu = new Menu\Menu();
        $this->entry = new Menu\Entry();
        $this->metaSource = $metaSource;
        $this->setMnLang($lang);
    }

    /**
     * @param string[] $metaSource
     * @throws MenuException
     * @return $this
     */
    public function setKey(array $metaSource): self
    {
        $this->metaSource->setSource($metaSource);
        return $this;
    }

    /**
     * @throws MenuException
     * @return bool
     */
    public function exists(): bool
    {
        return $this->metaSource->exists();
    }

    /**
     * @return Menu\Menu
     */
    public function getMenu(): Menu\Menu
    {
        return $this->menu;
    }

    public function reset(): void
    {
        $this->menu = new Menu\Menu();
        $this->workList = [];
        $this->highest = 0;
    }

    /**
     * @throws MenuException
     */
    public function load(): void
    {
        if (empty($this->menu->getEntries()) && empty($this->menu->getFile()) && $this->exists()) {
            $this->menu = $this->metaSource->load();
            $this->workList = $this->menu->getEntries();
            $this->highest = max([0] + array_map([$this, 'menuPosition'], $this->workList));
        }
    }

    public function menuPosition(Menu\Entry $item): int
    {
        return $item->getPosition();
    }

    public function updateInfo(?string $name, ?string $desc, ?int $displayCount): void
    {
        $this->menu->setData(
            $this->menu->getFile(),
            $name ?: $this->menu->getName(),
            $desc ?: $this->menu->getTitle(),
            $displayCount ?: $this->highest
        );
    }

    public function getEntry(string $id): ?Menu\Entry
    {
        foreach ($this->workList as &$entry) {
            if ($entry->getId() == $id) {
                return $entry;
            }
        }
        return null;
    }

    /**
     * @return Menu\Entry[]
     */
    public function getWorking(): array
    {
        $this->sortItems();
        return $this->workList;
    }

    public function addEntry(string $id, string $name = '', string $desc = '', bool $sub = false): void
    {
        if (!$this->getEntry($id)) {
            $name = empty($name) ? $id : $name;
            $item = clone $this->entry;
            $this->highest++;
            $this->workList[$id] = $item->setData($id, $name, $desc, $this->highest, $sub);
        }
    }

    /**
     * @param string $id
     * @param string|null $name
     * @param string|null $desc
     * @param bool|null $sub
     * @throws MenuException
     */
    public function updateEntry(string $id, ?string $name, ?string $desc, ?bool $sub): void
    {
        # null sign means not free, just unchanged
        $item = $this->getEntry($id);
        if (!$item) {
            throw new MenuException($this->getMnLang()->mnItemNotFound($id));
        }

        $item->setData(
            $id,
            is_null($name) ? $item->getName() : $name,
            is_null($desc) ? $item->getDesc() : $desc,
            $item->getPosition(),
            is_null($sub) ? $item->canGoSub() : $sub
        );
    }

    public function removeEntry(string $id): void
    {
        if ($item = $this->getEntry($id)) {
            unset($this->workList[$item->getId()]);
        }
    }

    /**
     * get assoc array with new positioning of files
     * key is file name, value is new position
     * @param array<string, int> $positions
     * @throws MenuException
     */
    public function rearrangePositions(array $positions): void
    {
        if (empty($positions)) {
            throw new MenuException($this->getMnLang()->mnProblematicData());
        }
        $matrix = [];
        # all at first
        foreach ($this->workList as &$item) {
            $matrix[$item->getPosition()] = $item->getPosition();
        }
        # updated at second
        foreach ($positions as $id => &$position) {
            if (empty($this->workList[$id])) {
                throw new MenuException($this->getMnLang()->mnItemNotFound($id));
            }
            if (!is_numeric($position)) {
                throw new MenuException($this->getMnLang()->mnProblematicData());
            }
            $matrix[$this->workList[$id]->getPosition()] = intval($position);
        }

        $prepared = [];
        foreach ($matrix as $old => &$new) {
            if ($old == $new) { # don't move, stay there
                $prepared[$new] = $old;
                unset($matrix[$old]);
            }
        }

        while (0 < count($matrix)) {
            foreach ($matrix as $old => &$new) {
                if (!isset($prepared[$new])) { # nothing on new position
                    $prepared[$new] = $old;
                    unset($matrix[$old]);
                } else {
                    $matrix[$old]++; # on next round try next position
                }
            }
        }

        $prepared = array_flip($prepared); # flip positions back, index is original one, not new one
        foreach ($this->workList as &$item) {
            $item->setPosition($prepared[$item->getPosition()]);
        }
    }

    public function clearData(): self
    {
        $this->highest = $this->getHighestKnownPosition();
        $this->clearHoles();
        $this->highest = $this->getHighestKnownPosition();
        return $this;
    }

    protected function clearHoles(): void
    {
        $max = $this->highest;
        /** @var array<int, string> $use */
        $use = [];
        $hole = false;
        $j = 0;

        /** @var array<int, string> $workList */
        $workList = [];
        foreach ($this->workList as &$item) {
            $workList[$item->getPosition()] = $item->getId(); # old position contains file ***
        }

        for ($i = 0; $i <= $max; $i++) {
            if (!empty($workList[$i])) { # position contains data
                $use[$j] = $workList[$i]; # new position contains file named *** from old one...
                $j++;
                $hole = false;
            } elseif (!$hole) { # first free position
                $j++;
                $hole = true;
            }
            # more than one free position
        }
        $use = array_flip($use); # flip back to names as PK

        foreach ($this->workList as &$item) {
            $item->setPosition($use[$item->getId()]);
        }
    }

    protected function getHighestKnownPosition(): int
    {
        return intval(array_reduce($this->workList, [$this, 'maxPosition'], 0));
    }

    public function maxPosition(int $carry, Menu\Entry $item): int
    {
        return intval(max($carry, $item->getPosition()));
    }

    /**
     * @throws MenuException
     */
    public function save(): void
    {
        $this->menu->setData( // reset entries from working list
            $this->menu->getFile(),
            $this->menu->getName(),
            $this->menu->getTitle(),
            $this->menu->getDisplayCount()
        );
        foreach ($this->workList as &$item) {
            $this->menu->addItem($item);
        }
        $this->metaSource->save($this->menu);
    }

    protected function sortItems(): void
    {
        uasort($this->workList, [$this, 'sortWorkList']);
    }

    public function sortWorkList(Menu\Entry $a, Menu\Entry $b): int
    {
        return $a->getPosition() <=> $b->getPosition();
    }
}
