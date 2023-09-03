<?php

namespace kalanis\kw_routed_paths\Linking;


use kalanis\kw_paths\Interfaces\IPaths;
use kalanis\kw_paths\Stuff;
use kalanis\kw_routed_paths\Support;


/**
 * Class Link
 * @package kalanis\kw_routed_paths\Linking
 * The reverse side of class
 * @see \kalanis\kw_routed_paths\Sources\Request
 */
class Link
{
    /** @var string */
    protected $prefix = '';

    public function __construct(string $prefix = '')
    {
        $this->prefix = $prefix;
    }

    /**
     * @param string[] $path
     * @param string[] $module
     * @param bool $moduleSingle
     * @param string|null $user
     * @return string
     */
    public function link(array $path, array $module = [], bool $moduleSingle = false, ?string $user = null): string
    {
        $prefix = !empty($this->prefix) ? Stuff::canonize($this->prefix) . IPaths::SPLITTER_SLASH : '';
        $modulePath = !empty($module) ? [Support::prefixWithSeparator($moduleSingle ? Support::PREFIX_MOD_SINGLE : Support::PREFIX_MOD_NORMAL) . Support::requestFromModuleName($module)] : [];
        $userPath = !empty($user) ? [Support::prefixWithSeparator(Support::PREFIX_USER) . $user] : [];
        return $prefix . strval(implode(IPaths::SPLITTER_SLASH, array_filter(array_merge($modulePath, $userPath, $path))));
    }
}
