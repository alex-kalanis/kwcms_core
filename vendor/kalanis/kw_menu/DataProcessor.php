<?php

namespace kalanis\kw_menu;


use kalanis\kw_langs\Lang;
use kalanis\kw_menu\Interfaces\IMenu;


/**
 * Class DataProcessor
 * @package kalanis\kw_menu
 * Menu data processor - CRUD
 */
class DataProcessor
{
    /** @var string path to menu file */
    protected $path = '';
    /** @var Interfaces\IDataSource|null */
    protected $storage = null;
    /** @var Menu\Menu */
    protected $menu = null;
    /** @var Menu\Item */
    protected $item = null;
    /** @var int */
    protected $highest = 0;
    /** @var Menu\Item[] */
    protected $workList = [];

    public function __construct(Interfaces\IDataSource $storage)
    {
        $this->menu = new Menu\Menu();
        $this->item = new Menu\Item();
        $this->storage = $storage;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;
        $this->menu->clear();
        $this->highest = 0;
        $this->workList = [];
        return $this;
    }

    /**
     * @return bool
     * @throws MenuException
     */
    public function exists(): bool
    {
        return $this->storage->exists($this->path);
    }

    /**
     * @return Menu\Menu
     * @throws MenuException
     */
    public function getMenu(): Menu\Menu
    {
        $this->load();
        return $this->menu;
    }

    /**
     * @throws MenuException
     */
    public function load(): void
    {
        if (empty($this->menu->getItems()) && empty($this->menu->getFile()) && !empty($this->path)) {
            $lines = $this->readLines();
            $this->loadHeader(reset($lines));
            $this->loadItems(array_slice($lines, 1));
        }
    }

    /**
     * @return string[]
     * @throws MenuException
     */
    protected function readLines(): array
    {
        if ($this->storage->exists($this->path)) {
            return explode("\r\n", $this->storage->load($this->path));
        }
        throw new MenuException(Lang::get('menu.error.cannot_open'));
    }

    protected function loadHeader(string $line): void
    {
        $headData = explode(IMenu::SEPARATOR, $line);
        $this->menu->setData((string)$headData[0], (string)$headData[2], (string)$headData[3], (int)$headData[1]);
    }

    protected function loadItems(array $lines): void
    {
        foreach ($lines as $line) {
            if (empty($line)) {
                continue;
            }
            if (in_array($line[0], ['#', ';'])) {
                continue;
            }
            $data = explode(IMenu::SEPARATOR, $line);
            if (2 > count($data)) {
                continue;
            }
            $item = clone $this->item;
            $item->setData((string)$data[2], (string)$data[3], (string)$data[0], (int)$data[1], boolval(intval($data[4])));
            $this->menu->addItem($item);
            $this->highest = max($this->highest, $item->getPosition());
        }
        $this->workList = $this->menu->getItems();
    }

    public function updateInfo(string $name, string $desc, int $displayCount): void
    {
        $this->menu->setData(
            $this->menu->getFile(),
            $name ?: $this->menu->getName(),
            $desc ?: $this->menu->getTitle(),
            $displayCount ?: $this->highest
        );
    }

    public function getItem(string $file): ?Menu\Item
    {
        foreach ($this->workList as &$item) {
            if ($item->getFile() == $file) {
                return $item;
            }
        }
        return null;
    }

    /**
     * @return Menu\Item[]
     */
    public function getWorking(): array
    {
        return $this->workList;
    }

    public function add(string $file, string $name = '', string $desc = '', bool $sub = false): void
    {
        $name = empty($name) ? $file : $name;
        $item = clone $this->item;
        $this->highest++;
        $this->workList[$this->highest] = $item->setData($name, $desc, $file, $this->highest, $sub);
    }

    /**
     * @param string $file
     * @param string|null $name
     * @param string|null $desc
     * @param bool|null $sub
     * @throws MenuException
     */
    public function update(string $file, ?string $name, ?string $desc, ?bool $sub): void
    {
        # null sign means not free, just unchanged
        $item = $this->getItem($file);
        if (!$item) {
            throw new MenuException(Lang::get('menu.error.item_not_found', $file));
        }

        $item->setData(
            is_null($name) ? $item->getName() : $name,
            is_null($desc) ? $item->getTitle() : $desc,
            $file,
            $item->getPosition(),
            is_null($sub) ? $item->canGoSub() : $sub
        );
    }

    public function remove(string $file): void
    {
        $item = $this->getItem($file);
        if ($item) {
            unset($this->workList[$item->getPosition()]);
        }
    }

    /**
     * @param int[] $positions
     * @throws MenuException
     */
    public function newPositions(array $positions): void
    {
        # get assoc array with new positioning of files
        $prepared = [];
        foreach ($positions as $old => &$new) {
            if (!is_numeric($old) || !is_numeric($new)) {
                throw new MenuException(Lang::get('menu.error.problematic_data'));
            }
        }

        foreach ($positions as $old => &$new) { # move staying
            if ($old == $new) {
                $prepared[$new] = $this->workList[$old];
            }
            unset($positions[$old]);
        }

        while (count($positions) > 0) {
            foreach ($positions as $old => &$new) {
                if (!isset($prepared[$new])) {
                    $prepared[$new] = $this->workList[$old];
                    unset($positions[$old]);
                } else {
                    $positions[$old]++;
                }
            }
        }
        $this->workList = $prepared;
    }

    public function clearData(): self
    {
        $this->clearRepeating();
        $this->clearHoles();
        $this->updatePositions();
        return $this;
    }

    protected function clearRepeating(): void
    {
        $rep = [];
        foreach ($this->workList as $i => &$item) { # indexing by names - last one stay alive
            $rep[$item->getFile()] = $i;
        }

        $use = [];
        foreach ($rep as $i) { # get meta with new indexes
            $use[$i] = $this->workList[$i];
            $this->highest = max($this->highest, $i);
        }

        $this->workList = $use; # save as unique ones
    }

    protected function clearHoles(): void
    {
        $max = $this->highest;
        $use = [];
        $hole = false;
        $j = 0;

        for ($i = 0; $i < $max; $i++) {
            if (!empty($this->workList[$i])) { # position contains data
                $use[$j] = $this->workList[$i];
                $j++;
                $hole = false;
            } elseif (!$hole) { # first free position
                $j++;
                $hole = true;
            }
            # more than one free position
        }

        $this->highest = $j;
        $this->workList = $use;
    }

    protected function updatePositions(): void
    {
        $this->highest = 0;
        foreach ($this->workList as $pos => &$item) {
            $item->setData(
                $item->getName(),
                $item->getTitle(),
                $item->getFile(),
                $pos,
                $item->canGoSub()
            );
            $this->highest = max($this->highest, $pos);
        }
    }

    /**
     * @throws MenuException
     */
    public function save(): void
    {
        $content = [];
        $content[] = implode(IMenu::SEPARATOR, [ // header
            $this->menu->getFile(),
            $this->menu->getDisplayCount(),
            $this->menu->getName(),
            $this->menu->getTitle(),
            '',
        ]);
        $content[] = '#';
        foreach ($this->workList as $item) { // save all! Limit only on render
            $content[] = implode(IMenu::SEPARATOR, [
                $item->getFile(),
                strval($item->getPosition()),
                $item->getName(),
                $item->getTitle(),
                strval(intval($item->canGoSub())),
                '',
            ]);
        }
        $content[] = '';

        $this->storage->save($this->path, implode("\r\n", $content));
    }
}
