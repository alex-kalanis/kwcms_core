<?php

namespace KWCMS\modules\Upload\Lib;


use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\UploadPerPartes;


/**
 * Class Uploader
 * @package kalanis\UploadPerPartes
 */
class Uploader extends UploadPerPartes\Uploader
{
    /** @var CompositeAdapter */
    protected $files = null;
    /** @var UploadPerPartes\Interfaces\IUPPTranslations */
    protected $lang = null;

    public function __construct(CompositeAdapter $files, UploadPerPartes\Interfaces\IUPPTranslations $lang)
    {
        $this->files = $files;
        $this->lang = $lang;
        parent::__construct();
    }

    protected function getTranslations(): UploadPerPartes\Interfaces\IUPPTranslations
    {
        return $this->lang;
    }

    protected function getInfoStorage(?UploadPerPartes\Interfaces\IUPPTranslations $lang = null): UploadPerPartes\Interfaces\IInfoStorage
    {
        return new UploadPerPartes\InfoStorage\Files($this->files, $lang);
    }

    protected function getDataStorage(?UploadPerPartes\Interfaces\IUPPTranslations $lang = null): UploadPerPartes\Interfaces\IDataStorage
    {
        return new UploadPerPartes\DataStorage\Files($this->files, $lang);
    }
}
