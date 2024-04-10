<?php

namespace kalanis\kw_mime\Check;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\IFLTranslations;
use kalanis\kw_files\Interfaces\IProcessFiles;
use kalanis\kw_files\Traits\TFile;
use kalanis\kw_mime\Interfaces\IMimeChecks;
use kalanis\kw_mime\Interfaces\IMiTranslations;
use kalanis\kw_mime\MimeException;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;


/**
 * Class ResourceFiles
 * @package kalanis\kw_mime\Check
 * System library to detect the mime type by files processor - pass as resource
 */
class ResourceFiles implements IMimeChecks
{
    use Traits\TResult;
    use Traits\TToResource;
    use TFile;

    protected ArrayPath $pathLib;

    public function __construct(?IFLTranslations $flLang = null, ?IMiTranslations $miLang = null)
    {
        $this->setFlLang($flLang);
        $this->setMiLang($miLang);
        $this->pathLib = new ArrayPath();
    }

    public function canUse($source): bool
    {
        if (!$this->isMimeFunction()) {
            return false;
        }
        if (is_object($source) && ($source instanceof IProcessFiles)) {
            $this->setProcessFile($source);
            return true;
        }
        return false;
    }

    public function getMime(array $path): string
    {
        $this->checkMimeFunction();
        try {
            $content = $this->getProcessFile()->readFile($path);
            $resource = $this->readSourceToResource(Stuff::arrayToPath($path), $content);
            return $this->determineResult(mime_content_type($resource));
        } catch (FilesException | PathsException $ex) {
            throw new MimeException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
