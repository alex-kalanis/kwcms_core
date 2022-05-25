<?php

namespace KWCMS\modules\File;


use kalanis\kw_address_handler\Headers;
use kalanis\kw_confs\Config;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_mime\MimeType;
use kalanis\kw_modules\AModule;
use kalanis\kw_modules\Linking\InternalLink;
use kalanis\kw_modules\Output\AOutput;
use kalanis\kw_modules\Output\Raw;
use kalanis\kw_paths\Stuff;


/**
 * Class File
 * @package KWCMS\modules\File
 * Users files - for access that which are not available the direct way
 * @link https://stackoverflow.com/questions/3303029/http-range-header
 *
 * test$ curl -v -r 200- http://kwcms_core.lemp.test/web/ms:file/velke_soubory/calcline.txt
 */
class File extends AModule
{
    protected $mime = null;
    protected $extLink = null;
    protected $intLink = null;
    /** @var Lib\SizeAdapters\AAdapter|null */
    protected $sizeAdapter = null;

    public function __construct()
    {
        Config::load(static::getClassName(static::class));
        $this->mime = new MimeType(true);
        $this->intLink = new InternalLink(Config::getPath());
    }

    public function process(): void
    {
    }

    public function output(): AOutput
    {
        $out = new Raw();
        $filePath = $this->intLink->userContent(Config::getPath()->getPath());
        $protocol = $this->inputs->getInArray('SERVER_PROTOCOL', [IEntry::SOURCE_SERVER]);
        if (!$filePath) {
            Headers::setCustomCode(strval(reset($protocol)), 404);
            return $out;
        }

        $wantOnlyHeaders = $this->onlyHeaders();

        $this->parseRanges($filePath);
        if (!$this->sizeAdapter->inAllowedRange()) {
            Headers::setCustomCode(strval(reset($protocol)), 416);
            return $out;
        }
        if (!$this->isAccessible($filePath)) {
            Headers::setCustomCode(strval(reset($protocol)), 500);
            return $out;
        }

        // pass only part
        $maxTransferSize = intval(strval(Config::get('File', 'max_size', 16777216)));
        if ($maxTransferSize < $this->sizeAdapter->getUsableLength()) {
            $this->sizeAdapter->onlyPart($maxTransferSize);
        }

        // headers
        header('Content-Type: ' . $this->mime->mimeByPath($filePath));
        header('Cache-Control: public, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Accept-Ranges: bytes, seek');
        header('Last-Modified: ' . date('r', filemtime($filePath)));

        if ($wantOnlyHeaders) {
            header('Content-Length: ' . $this->sizeAdapter->getMaxLength());
            return $out;
        }

        header('Content-Transfer-Encoding: binary');

        // pass name and download flag
        $name = Stuff::filename($filePath);
        $down = $this->inputs->getInArray('download');
        if (!empty($down) && boolval(intval(strval(reset($down))))) {
            header('Content-Disposition: attachment; filename="' . $name . '"');
        } else {
            header('Content-Disposition: inline; filename="' . $name . '"');
        }

        if ($this->sizeAdapter->usedRange()) {
            $result = new Lib\Output();
            Headers::setCustomCode(strval(reset($protocol)), 206);
            if ($contentRange = $this->sizeAdapter->contentRange()) {
                header($contentRange);
            }
            return $result->setAdapter($this->sizeAdapter);
        }

        // TODO: out - everything through Lib\Output
        // left just headers for files larger than limit
        return $out->setContent(strval(@file_get_contents($filePath)));
    }

    protected function isAccessible(string $filePath): bool
    {
        return is_file($filePath) && is_readable($filePath);
    }

    protected function onlyHeaders(): bool
    {
        $requestMethod = $this->inputs->getInArray('REQUEST_METHOD', [IEntry::SOURCE_SERVER]);
        return !empty($requestMethod) && ('HEAD' == strtoupper(strval(reset($requestMethod))));
    }

    /**
     * @link https://www.php.net/manual/en/function.fread.php#84115
     * @link http://tools.ietf.org/id/draft-ietf-http-range-retrieval-00.txt
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Range_requests
     * !! BEWARE !! Chrome want the whole file even if it's too large -> return only first segment with 206 header
     */
    protected function parseRanges(string $filePath): void
    {
        $fileSize = filesize($filePath);
        $range = $this->inputs->getInArray('HTTP_RANGE', [IEntry::SOURCE_SERVER]);
        if (!empty($range)) {
            $line = strval(reset($range));
            list($sizeUnit, $range) = explode('=', $line, 2);
            $this->sizeAdapter = Lib\SizeAdapters\Factory::getAdapter($sizeUnit);
            $this->sizeAdapter->fillFileDetails(
                $filePath,
                $fileSize,
                intval(strval(Config::get('File', 'step_by', 16384)))
            );
            $this->sizeAdapter->parseRanges($range);
        } else {
            $this->sizeAdapter = new Lib\SizeAdapters\Bytes();
            $this->sizeAdapter->fillFileDetails(
                $filePath,
                $fileSize,
                intval(strval(Config::get('File', 'step_by', 16384)))
            );
        }

    }
}
