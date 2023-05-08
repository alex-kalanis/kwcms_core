<?php

namespace kalanis\kw_mime\Check;


use kalanis\kw_files\Interfaces\IProcessFiles;
use kalanis\kw_mime\Interfaces\IMime;
use kalanis\kw_mime\Interfaces\IMiTranslations;
use kalanis\kw_storage\Storage;


/**
 * Class Factory
 * @package kalanis\kw_mime\Check
 * Which library will run?
 */
class Factory
{
    use Traits\TCheckCalls;

    /** @var IMiTranslations|null */
    protected $lang = null;

    public function __construct(?IMiTranslations $lang = null)
    {
        $this->lang = $lang;
    }

    /**
     * @param IProcessFiles|Storage|string $source
     * @return IMime
     */
    public function getLibrary($source): IMime
    {
        if ($this->isMimeFunction()) {
            if ($source instanceof IProcessFiles) {
                $lib = new DataFiles(null, $this->lang);
                $lib->canUse($source);
                return $lib;
            } elseif ($source instanceof Storage) {
                $lib = new DataStorage($this->lang);
                $lib->canUse($source);
                return $lib;
            } elseif (is_string($source)) {
                $lib = new LocalVolume1($this->lang);
                $lib->canUse($source);
                return $lib;
            } else {
                return new CustomList();
            }
        } else {
            return new CustomList();
        }
    }
}
