<?php

namespace kalanis\kw_mime\Check\Traits;


use kalanis\kw_mime\MimeException;
use kalanis\kw_storage\Storage;


trait TStorage
{
    use TLang;

    /** @var Storage|null */
    protected $storage = null;

    public function setStorage(Storage $lang = null): void
    {
        $this->storage = $lang;
    }

    /**
     * @throws MimeException
     * @return Storage
     */
    public function getStorage(): Storage
    {
        if (empty($this->storage)) {
            throw new MimeException($this->getMiLang()->miNoStorage());
        }
        return $this->storage;
    }
}
