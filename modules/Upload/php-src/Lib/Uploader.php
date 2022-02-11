<?php

namespace KWCMS\modules\Upload\Lib;


use kalanis\UploadPerPartes;


/**
 * Class Uploader
 * @package kalanis\UploadPerPartes
 */
class Uploader extends UploadPerPartes\Uploader
{
    protected function getTranslations(): UploadPerPartes\Interfaces\IUPPTranslations
    {
        return new Translations();
    }
}
