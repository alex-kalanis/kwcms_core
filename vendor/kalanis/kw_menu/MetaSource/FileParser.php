<?php

namespace kalanis\kw_menu\MetaSource;


use kalanis\kw_menu\Interfaces;
use kalanis\kw_menu\Menu\Entry;
use kalanis\kw_menu\Menu\Menu;
use kalanis\kw_menu\Traits\TEscape;


/**
 * Class FileParser
 * @package kalanis\kw_menu\MetaSource
 * Process metadata as file on volume
 */
class FileParser implements Interfaces\IMetaFileParser
{
    use TEscape;

    protected Menu $menu;
    protected Entry $entry;

    public function __construct()
    {
        $this->menu = new Menu();
        $this->entry = new Entry();
    }

    /**
     * @param string $content
     * @return Menu
     */
    public function unpack(string $content): Menu
    {
        $menu = clone $this->menu;
        if (!empty($content) && (false !== mb_strpos($content, "\r\n"))) {
            $lines = explode("\r\n", $content);
            $this->loadHeader($menu, reset($lines));
            $this->loadItems($menu, array_slice($lines, 1));
        }
        return $menu;
    }

    protected function loadHeader(Menu $menu, string $line): void
    {
        if (false === mb_strpos($line, Interfaces\IMenu::SEPARATOR)) {
            return;
        }
        $headData = explode(Interfaces\IMenu::SEPARATOR, $line);
        $menu->setData(
            $this->restoreNl(strval($headData[0])),
            $this->restoreNl(strval($headData[2])),
            $this->restoreNl(strval($headData[3])),
            intval($headData[1])
        );
    }

    /**
     * @param Menu $menu
     * @param string[] $lines
     */
    protected function loadItems(Menu $menu, array $lines): void
    {
        foreach ($lines as $line) {
            if (empty($line)) {
                continue;
            }
            if (in_array($line[0], ['#', ';'])) {
                continue;
            }
            $data = explode(Interfaces\IMenu::SEPARATOR, $line);
            if (2 > count($data)) {
                continue;
            }
            $item = clone $this->entry;
            $item->setData(
                $this->restoreNl(strval($data[0])),
                $this->restoreNl(strval($data[2])),
                $this->restoreNl(strval($data[3])),
                intval($data[1]),
                boolval(intval($data[4]))
            );
            $menu->addItem($item);
        }
    }

    public function pack(Menu $menu): string
    {
        $content = [];
        $content[] = implode(Interfaces\IMenu::SEPARATOR, [ // header
            $this->escapeNl($menu->getFile()),
            $menu->getDisplayCount(),
            $this->escapeNl($menu->getName()),
            $this->escapeNl($menu->getTitle()),
            '',
        ]);
        $content[] = '#';
        foreach ($menu->getEntries() as $item) { // save all! Limit only on render
            $content[] = implode(Interfaces\IMenu::SEPARATOR, [
                $this->escapeNl($item->getId()),
                strval($item->getPosition()),
                $this->escapeNl($item->getName()),
                $this->escapeNl($item->getDesc()),
                strval(intval($item->canGoSub())),
                '',
            ]);
        }
        $content[] = '';

        return implode("\r\n", $content);
    }
}
