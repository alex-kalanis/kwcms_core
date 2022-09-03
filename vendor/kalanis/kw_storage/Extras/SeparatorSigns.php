<?php

namespace kalanis\kw_storage\Extras;


/**
 * Trait TRemoveCycle
 * @package kalanis\kw_storage\Extras
 * low-level work with extended dirs - remove dirs and files in cycle - everything with subdirectories
 */
class SeparatorSigns
{
    /**
     * Remove separator sign from path end
     * @param string $path
     * @param string $separator
     * @return string
     */
    public static function removeFromEnd(string $path, string $separator = DIRECTORY_SEPARATOR): string
    {
        $signLen = mb_strlen($separator);
        return ($separator == mb_substr($path, -1 * $signLen)) ? mb_substr($path, 0, -1 * $signLen) : $path ;
    }

    /**
     * Remove separator sign from path start
     * @param string $path
     * @param string $separator
     * @return string
     */
    public static function removeFromStart(string $path, string $separator = DIRECTORY_SEPARATOR): string
    {
        $signLen = mb_strlen($separator);
        return ($separator == mb_substr($path, 0, $signLen)) ? mb_substr($path, $signLen) : $path ;
    }
}
