<?php

namespace kalanis\kw_input\Loaders;


use kalanis\kw_input\Entries;


/**
 * Class Json
 * @package kalanis\kw_input\Loaders
 * Load Json input array into normalized entries
 */
class Json extends ALoader
{
    public function loadVars(string $source, $array): array
    {
        $fileEntries = new Entries\FileEntry();
        $entries = new Entries\Entry();
        $result = [];
        foreach ($array as $postedKey => $posted) {
            if (is_array($posted) && isset($posted['FILE'])) {
                $file = tempnam(sys_get_temp_dir(), 'js_');
                if (false === $file) {
                    // @codeCoverageIgnoreStart
                    continue;
                }
                // @codeCoverageIgnoreEnd
                $size = file_put_contents($file, $posted['FILE']);

                $entry = clone $fileEntries;
                $entry->setEntry($source, strval($postedKey));
                $entry->setFile(
                    $postedKey . '.json',
                    $file,
                    'application/octet-stream',
                    0,
                    intval($size)
                );
                $result[] = $entry;
            } else {
                $entry = clone $entries;
                $entry->setEntry($source, strval($postedKey), $posted);
                $result[] = $entry;
            }
        }
        return $result;
    }
}
