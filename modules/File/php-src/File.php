<?php

namespace KWCMS\modules\File;


use kalanis\kw_address_handler\Headers;
use kalanis\kw_confs\Config;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_mime\MimeType;
use kalanis\kw_modules\AModule;
use kalanis\kw_modules\InternalLink;
use kalanis\kw_modules\Output\AOutput;
use kalanis\kw_modules\Output\Raw;
use kalanis\kw_paths\Stuff;


/**
 * Class File
 * @package KWCMS\modules\File
 * Users files - for access that which are not available the direct way
 *
 * test$ curl -v -r 200- http://kwcms_core.lemp.test/web/ms:file/velke_soubory/calcline.txt
 */
class File extends AModule
{
    protected $mime = null;
    protected $extLink = null;
    protected $intLink = null;
    protected $seek = null;

    public function __construct()
    {
        Config::load(static::getClassName(static::class));
        $this->mime = new MimeType(true);
        $this->intLink = new InternalLink(Config::getPath());
        $this->seek = new Seek();
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
        $this->prepareSeeks($filePath);
        $this->parseRanges();
        if (!$this->inAllowedRange()) {
            Headers::setCustomCode(strval(reset($protocol)), 416);
            return $out;
        }
        if (!$this->checkMaxPassedSize()) {
            Headers::setCustomCode(strval(reset($protocol)), 413);
            return $out;
        }
        if (!$this->isAccessible($filePath)) {
            Headers::setCustomCode(strval(reset($protocol)), 500);
            return $out;
        }

        // headers
        header('Content-Type: ' . $this->mime->mimeByPath($filePath));
        header('Cache-Control: public, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Accept-Ranges: bytes');
        header('Last-Modified: ' . date('r', filemtime($filePath)));
        header('Content-Transfer-Encoding: binary');

        // pass name and download flag
        $name = Stuff::filename($filePath);
        $down = $this->inputs->getInArray('download');
        if (!empty($down) && boolval(intval(strval(reset($down))))) {
            header('Content-Disposition: attachment; filename="' . $name . '"');
        } else {
            header('Content-Disposition: inline; filename="' . $name . '"');
        }

        if ($this->seek->usedRange()) {
            $result = new Output();
            if ($this->seek->getStart() > 0 || $this->seek->getEnd() < $this->seek->getMax()) {
                Headers::setCustomCode(strval(reset($protocol)), 206);
                header(sprintf(
                    'Content-Range: bytes %d-%d/%d',
                    $this->seek->getStart(),
                    $this->seek->getEnd(),
                    $this->seek->getMax() + 1)
                );
            }
            return $result->setSeek($this->seek);
        }

        return $out->setContent(strval(@file_get_contents($filePath)));
    }

    protected function prepareSeeks(string $filePath): void
    {
        $max = filesize($filePath) - 1;
        $this->seek
            ->setData($filePath, $max)
            ->setLimits(0, $max)
            ->setStepBy(intval(strval(Config::get('File', 'step_by', 16384))))
        ;
    }

    protected function checkMaxPassedSize(): bool
    {
        $maxTransferSize = intval(strval(Config::get('File', 'max_size', 16777216)));
        return ($maxTransferSize >= $this->seek->getUsableLength());
    }

    protected function inAllowedRange(): bool
    {
        return $this->seek->getStart() <= $this->seek->getMax()
            && $this->seek->getEnd() <= $this->seek->getMax();
    }

    protected function isAccessible(string $filePath): bool
    {
        return is_file($filePath) && is_readable($filePath);
    }

    /**
     * @link https://www.php.net/manual/en/function.fread.php#84115
     * @link http://tools.ietf.org/id/draft-ietf-http-range-retrieval-00.txt
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Range_requests
     */
    protected function parseRanges(): void
    {
        $range = $this->inputs->getInArray('HTTP_RANGE', [IEntry::SOURCE_SERVER]);
        if (!empty($range)) {
            $line = strval(reset($range));
            list($sizeUnit, $range) = explode('=', $line, 2);
            if ('bytes' == $sizeUnit) {
                if (false !== strpos($range, ',')) {
                    // multiple ranges could be specified at the same time
                    // but for the sake of sanity only serve the first range
                    list($range, $extraRangesNotServed) = explode(',', $range, 2);
                }

                // figure out download piece from range (if set)
                list($seekStart, $seekEnd) = explode('-', $range, 2);

                // set start and end based on range (if set), else set defaults
                // also check for invalid ranges.
                $seekEnd = (empty($seekEnd))
                    ? $this->seek->getMax()
                    : min(abs(intval($seekEnd)), $this->seek->getMax());
                $seekStart = (empty($seekStart) || abs(intval($seekStart))) > $seekEnd
                    ? 0
                    : max(abs(intval($seekStart)), 0);

                $this->seek->setLimits($seekStart, $seekEnd)->useRange(true);
            }
        }
    }
}
