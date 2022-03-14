<?php

namespace kalanis\kw_menu\Interfaces;


use kalanis\kw_menu\Menu\Menu;
use kalanis\kw_menu\MenuException;


/**
 * Interface IMetaSource
 * @package kalanis\kw_menu\Interfaces
 * Which actions are supported by data sources of meta content
 */
interface IMetaSource
{
    /**
     * @param string $groupKey
     * @throws MenuException
     */
    public function setSource(string $groupKey): void;

    /**
     * @return bool
     * @throws MenuException
     */
    public function exists(): bool;

    /**
     * @return Menu
     * @throws MenuException
     */
    public function load(): Menu;

    /**
     * @param Menu $content
     * @return bool
     * @throws MenuException
     */
    public function save(Menu $content): bool;
}
