<?php

namespace kalanis\kw_mime\Check;


use kalanis\kw_mime\Interfaces\IMimeChecks;
use kalanis\kw_mime\Interfaces\IMiTranslations;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\PathsException;


/**
 * Class ALocalVolume
 * @package kalanis\kw_mime\Check
 * System libraries to get the mime type - works over local volume, not on remote files
 */
abstract class ALocalVolume implements IMimeChecks
{
    use Traits\TCheckCalls;
    use Traits\TResult;

    protected string $startPath = '';
    protected ArrayPath $pathLib;

    public function __construct(?IMiTranslations $lang = null)
    {
        $this->setMiLang($lang);
        $this->pathLib = new ArrayPath();
    }

    public function canUse($source): bool
    {
        if (!$this->hasDependencies()) {
            return false;
        }
        if (!is_string($source)) {
            return false;
        }
        $path = realpath($source);
        if (false === $path) {
            return false;
        }
        $this->startPath = $path;
        return true;
    }

    abstract protected function hasDependencies(): bool;

    /**
     * @param string[] $path
     * @throws PathsException
     * @return string
     */
    protected function pathOnVolume(array $path): string
    {
        return $this->startPath . DIRECTORY_SEPARATOR . $this->pathLib->setArray($path)->getString();
    }
}
