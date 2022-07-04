<?php

namespace kalanis\kw_storage\Extras;


/**
 * Trait TRemoveCycle
 * @package kalanis\kw_storage\Extras
 * low-level work with extended dirs - remove dirs and files in cycle - everything with subdirectories
 */
trait TRemoveCycle
{
    /**
     * Remove sub dirs and their content recursively
     * @param string $dirPath
     * @param string $sign
     * @return bool
     */
    protected function removeCycle(string $dirPath, string $sign = DIRECTORY_SEPARATOR): bool
    {
        $path = static::removeEndingSign($dirPath, $sign);
        if (is_dir($path) && $fileListing = scandir($path)) {
            foreach ($fileListing as $fileName) {
                if (is_dir($path . $sign . $fileName)) {
                    if (('.' != $fileName) && ('..' != $fileName)) {
                        $this->removeCycle($path . $sign . $fileName);
                        rmdir($path . $sign . $fileName);
                    }
                } else {
                    unlink($path . $sign . $fileName);
                }
            }
        }
        return true;
    }

    /**
     * Remove ending separator sign
     * @param string $path
     * @param string $sign
     * @return string
     */
    public static function removeEndingSign(string $path, string $sign = DIRECTORY_SEPARATOR): string
    {
        return ($sign == mb_substr($path, -1, 1)) ? mb_substr($path, 0, -1 * mb_strlen($sign)) : $path ;
    }
}
