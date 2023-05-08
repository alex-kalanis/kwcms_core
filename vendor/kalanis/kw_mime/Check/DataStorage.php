<?php

namespace kalanis\kw_mime\Check;


use kalanis\kw_mime\Interfaces\IMimeChecks;
use kalanis\kw_mime\Interfaces\IMiTranslations;
use kalanis\kw_mime\MimeException;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\PathsException;
use kalanis\kw_storage\Storage;
use kalanis\kw_storage\StorageException;


/**
 * Class DataStorage
 * @package kalanis\kw_mime\Check
 * System library to detect the mime type in storage
 */
class DataStorage implements IMimeChecks
{
    use Traits\TCheckCalls;
    use Traits\TResult;
    use Traits\TStorage;

    /** @var ArrayPath */
    protected $pathLib = null;

    public function __construct(?IMiTranslations $lang = null)
    {
        $this->setMiLang($lang);
        $this->pathLib = new ArrayPath();
    }

    public function canUse($source): bool
    {
        if (!$this->isMimeFunction()) {
            return false;
        }
        if (is_object($source) && ($source instanceof Storage)) {
            $this->setStorage($source);
            return true;
        }
        return false;
    }

    public function getMime(array $path): string
    {
        try {
            $this->checkMimeFunction();
            $target = $this->pathLib->setArray($path)->getString();
            $content = $this->getStorage()->get($target);
            $fileData = 'data://,' . urlencode(strval($content));
            return $this->determineResult(mime_content_type($fileData));
        } catch (StorageException | PathsException $ex) {
            throw new MimeException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
