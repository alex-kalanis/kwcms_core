<?php

namespace KWCMS\modules\Images\Lib;


use kalanis\kw_files\FilesException;
use kalanis\kw_images\Content\Images;
use kalanis\kw_langs\Lang;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;


/**
 * trait TLibExistence
 * @package KWCMS\modules\Images\Lib
 * Check for existence of file
 */
trait TLibExistence
{
    /**
     * @param Images $images
     * @param string[] $dir
     * @param string $fileName
     * @throws FilesException
     * @throws PathsException
     */
    protected function checkExistence(Images $images, array $dir, string $fileName): void
    {
        // no name or invalid file name -> redirect!
        if (empty($fileName)) {
            throw new FilesException(Lang::get('images.file_name.invalid', $fileName));
        }

        $path = array_merge($dir, [Stuff::canonize($fileName)]);
        if (!$images->exists($path)) {
            throw new FilesException(Lang::get('images.file_name.not_found', $fileName));
        }
    }
}
