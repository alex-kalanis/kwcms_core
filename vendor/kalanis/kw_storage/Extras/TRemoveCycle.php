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
        $path = realpath($dirPath);
        if (false === $path) {
            return false;
        }
        if (is_dir($path)) {
            $fileListing = scandir($path);
            if (!empty($fileListing)) {
                foreach ($fileListing as $fileName) {
                    if (is_dir($path . $sign . $fileName)) {
                        if (('.' != $fileName) && ('..' != $fileName)) {
                            $this->removeCycle($path . $sign . $fileName);
                        }
                    } else {
                        unlink($path . $sign . $fileName);
                    }
                }
            }
            rmdir($path);
        } elseif (is_file($path)) {
            unlink($path);
        }
        return true;
    }
}
