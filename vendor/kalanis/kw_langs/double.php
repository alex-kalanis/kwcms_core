<?php

// double underscore as simple key to access translations; also allows fill translation with preset values

if (!function_exists('__')) {
    /**
     * @param string|int $what
     * @param string ...$args
     * @return string
     */
    function __($what, ...$args): string
    {
        return \kalanis\kw_langs\Lang::get((string) $what, ...$args);
    }
}
