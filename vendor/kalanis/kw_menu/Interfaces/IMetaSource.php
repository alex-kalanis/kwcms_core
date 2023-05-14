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
     * @param string[] $groupKey
     * @throws MenuException
     */
    public function setSource(array $groupKey): void;

    /**
     * @throws MenuException
     * @return bool
     */
    public function exists(): bool;

    /**
     * @throws MenuException
     * @return Menu
     */
    public function load(): Menu;

    /**
     * @param Menu $content
     * @throws MenuException
     * @return bool
     */
    public function save(Menu $content): bool;
}
