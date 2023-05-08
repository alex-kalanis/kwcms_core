<?php

namespace kalanis\kw_mime\Check;


use kalanis\kw_mime\MimeException;
use kalanis\kw_paths\PathsException;


/**
 * Class LocalVolume2
 * @package kalanis\kw_mime\Check
 * System library to detect the mime type on local volume
 */
class LocalVolume2 extends ALocalVolume
{
    protected function hasDependencies(): bool
    {
        return $this->isMimeClass() && $this->isMimeMethod();
    }

    public function getMime(array $path): string
    {
        $this->checkMimeClass();
        $this->checkMimeFunction();
        try {
            $fileinfo = new \finfo(\FILEINFO_MIME); // file mimetype
            return $this->determineResult($fileinfo->buffer($this->pathOnVolume($path)));
        } catch (PathsException $ex) {
            throw new MimeException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
