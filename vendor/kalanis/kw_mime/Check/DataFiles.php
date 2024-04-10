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


/**
 * Class DataFiles
 * @package kalanis\kw_mime\Check
 * System library to detect the mime type by files processor
 */
class DataFiles implements IMimeChecks
{
    use Traits\TResult;
    use Traits\TToString;
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
        try {
            $target = $this->pathLib->setArray($path)->getString();
            $content = $this->getProcessFile()->readFile($path);
            $fileData = 'data://,' . urlencode($this->readSourceToString($target, $content));
            return $this->determineResult(mime_content_type($fileData));
        } catch (FilesException | PathsException $ex) {
            throw new MimeException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
