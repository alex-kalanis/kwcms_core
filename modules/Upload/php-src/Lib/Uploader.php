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

    public function __construct(CompositeAdapter $files)
    {
        $this->files = $files;
        parent::__construct();
    }

    protected function getTranslations(): UploadPerPartes\Interfaces\IUPPTranslations
    {
        return new Translations();
    }

    protected function getInfoStorage(): UploadPerPartes\Interfaces\IInfoStorage
    {
        return new UploadPerPartes\InfoStorage\Files($this->files, $this->lang);
    }

    protected function getDataStorage(): UploadPerPartes\Interfaces\IDataStorage
    {
        return new UploadPerPartes\DataStorage\Files($this->files, $this->lang);
    }
}
