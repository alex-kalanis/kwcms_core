<?php

namespace kalanis\kw_menu\Interfaces;


use kalanis\kw_menu\Menu\Menu;
use kalanis\kw_menu\MenuException;


/**
 * Interface IMetaFileParser
 * @package kalanis\kw_menu\Interfaces
 * Packing and unpacking the meta file from menu entries
 */
interface IMetaFileParser
{
    /**
     * @param string $content
     * @throws MenuException
     * @return Menu
     */
    public function unpack(string $content): Menu;

    /**
     * @param Menu $menu
     * @throws MenuException
     * @return string
     */
    public function pack(Menu $menu): string;
}
