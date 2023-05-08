<?php

namespace kalanis\kw_mime\Check;


use kalanis\kw_mime\MimeException;
use kalanis\kw_paths\PathsException;


/**
 * Class LocalVolume1
 * @package kalanis\kw_mime\Check
 * System library to detect the mime type on local volume
 */
class LocalVolume1 extends ALocalVolume
{
    protected function hasDependencies(): bool
    {
        return $this->isMimeMethod();
    }

    public function getMime(array $path): string
    {
        $this->checkMimeMethod();
        try {
            return $this->determineResult(mime_content_type($this->pathOnVolume($path)));
        } catch (PathsException $ex) {
            throw new MimeException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
