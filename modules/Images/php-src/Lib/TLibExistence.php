<?php

namespace KWCMS\modules\Images\Lib;


use kalanis\kw_images\Files;
use kalanis\kw_images\ImagesException;
use kalanis\kw_langs\Lang;


/**
 * trait TLibExistence
 * @package KWCMS\modules\Images\Lib
 * Check for existence of file
 */
trait TLibExistence
{
    /**
     * @param Files $files
     * @param string $dir
     * @param string $fileName
     * @throws ImagesException
     */
    protected function checkExistence(Files $files, string $dir, string $fileName): void
    {
        // no name or invalid file name -> redirect!
        if (empty($fileName)) {
            throw new ImagesException(Lang::get('images.file_name.invalid', $fileName));
        }
        try {
            $files->getLibGraphics()->check(
                $dir . DIRECTORY_SEPARATOR . $fileName
            );
        } catch (ImagesException $ex) {
            throw new ImagesException(Lang::get('images.file_name.not_found', $fileName), 0, $ex);
        }
    }
}
