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
 * System library to detect the mime type by files processor - pass as file in temp dir
 */
class TempFiles implements IMimeChecks
{
    use Traits\TResult;
    use Traits\TToLocalFile;
    use TFile;

    /** @var ArrayPath */
    protected $pathLib = null;

    public function __construct(?IFLTranslations $flLang = null, ?IMiTranslations $miLang = null)
    {
        $this->setLang($flLang);
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
        $tempFile = tempnam(sys_get_temp_dir(), 'MimeCheck');
        if (false === $tempFile) {
            // @codeCoverageIgnoreStart
            throw new MimeException($this->getMiLang()->miCannotLoadTempFile());
        }
        // @codeCoverageIgnoreEnd

        $this->checkMimeFunction();
        try {
            $content = $this->getProcessFile()->readFile($path);
            $this->readSourceToLocalFile(Stuff::arrayToPath($path), $content, $tempFile);
            $mime = mime_content_type($tempFile);
        } catch (FilesException | PathsException $ex) {
            throw new MimeException($ex->getMessage(), $ex->getCode(), $ex);
        } finally {
            @unlink($tempFile);
        }
        return $this->determineResult($mime);
    }
}
