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
    use Traits\TLang;
    use Traits\TCheckCalls;

    public function __construct(?IMiTranslations $lang = null)
    {
        $this->setMiLang($lang);
    }

    /**
     * @param IProcessFiles|Storage|string $source
     * @return IMime
     */
    public function getLibrary($source): IMime
    {
        if ($this->isMimeFunction()) {
            if (is_object($source) && ($source instanceof IProcessFiles)) {
                $lib = new DataFiles(null, $this->getMiLang());
                $lib->canUse($source);
                return $lib;
            } elseif (is_object($source) && ($source instanceof Storage)) {
                $lib = new DataStorage($this->getMiLang());
                $lib->canUse($source);
                return $lib;
            } elseif (is_string($source)) {
                $lib = new LocalVolume1($this->getMiLang());
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
