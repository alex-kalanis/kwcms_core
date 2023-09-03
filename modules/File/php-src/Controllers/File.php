<?php

namespace KWCMS\modules\File\Controllers;


use kalanis\kw_address_handler\Headers;
use kalanis\kw_confs\ConfException;
use kalanis\kw_confs\Config;
use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_files\Access\Factory;
use kalanis\kw_files\FilesException;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_mime\Check;
use kalanis\kw_mime\Interfaces\IMime;
use kalanis\kw_mime\MimeException;
use kalanis\kw_modules\Output\AOutput;
use kalanis\kw_modules\Output\Raw;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stored;
use kalanis\kw_routed_paths\StoreRouted;
use kalanis\kw_user_paths\InnerLinks;
use KWCMS\modules\Core\Libs\AModule;
use KWCMS\modules\Core\Libs\FilesTranslations;
use KWCMS\modules\File\Lib;


/**
 * Class File
 * @package KWCMS\modules\File\Controllers
 * Users files - for access that which are not available the direct way
 * @link https://stackoverflow.com/questions/3303029/http-range-header
 *
 * test$ curl -v -r 200- http://kwcms_core.lemp.test/web/ms:file/velke_soubory/calcline.txt
 */
class File extends AModule
{
    /** @var IMime */
    protected $mime = null;
    /** @var Lib\SizeAdapters\AAdapter|null */
    protected $sizeAdapter = null;
    /** @var ArrayPath */
    protected $arrPath = null;
    /** @var CompositeAdapter */
    protected $files = null;
    /** @var InnerLinks */
    protected $innerLink = null;

    /**
     * @param mixed ...$constructParams
     * @throws ConfException
     * @throws FilesException
     * @throws PathsException
     */
    public function __construct(...$constructParams)
    {
        Config::load(static::getClassName(static::class));
        $this->arrPath = new ArrayPath();
        $this->innerLink = new InnerLinks(
            StoreRouted::getPath(),
            boolval(Config::get('Core', 'site.more_users', false)),
            boolval(Config::get('Core', 'page.more_lang', false))
        );
        $this->files = (new Factory(new FilesTranslations()))->getClass(
            Stored::getPath()->getDocumentRoot() . Stored::getPath()->getPathToSystemRoot()
        );
        $this->mime = (new Check\Factory())->getLibrary(null);
    }

    public function process(): void
    {
    }

    /**
     * @throws FilesException
     * @throws MimeException
     * @throws PathsException
     * @return AOutput
     */
    public function output(): AOutput
    {
        $out = new Raw();
        $filePath = $this->innerLink->toFullPath(StoreRouted::getPath()->getPath());
        $protocol = $this->inputs->getInArray('SERVER_PROTOCOL', [IEntry::SOURCE_SERVER]);
        if (!$this->files->exists($filePath)) {
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

        $name = $this->arrPath->setArray($filePath)->getFileName();
        // headers
        header('Content-Type: ' . $this->mime->getMime($filePath));
        header('Cache-Control: public, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Accept-Ranges: bytes, seek');
        header('Last-Modified: ' . date('r', $this->files->created($filePath)));

        if ($wantOnlyHeaders) {
            header('Content-Length: ' . $this->sizeAdapter->getMaxLength());
            return $out;
        }

        header('Content-Transfer-Encoding: binary');

        // pass name and download flag
        $down = $this->inputs->getInArray('download');
        if (!empty($down) && boolval(intval(strval(reset($down))))) {
            header('Content-Disposition: attachment; filename="' . $name . '"');
        } else {
            header('Content-Disposition: inline; filename="' . $name . '"');
        }

        $result = new Lib\Output($this->files, $name, $filePath);
        if ($this->sizeAdapter->usedRange()) {
            Headers::setCustomCode(strval(reset($protocol)), 206);
            if ($contentRange = $this->sizeAdapter->contentRange()) {
                header($contentRange);
            }
            return $result->setAdapter($this->sizeAdapter);
        }

        return $result;
    }

    /**
     * @param string[] $filePath
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    protected function isAccessible(array $filePath): bool
    {
        return $this->files->isFile($filePath) && $this->files->isReadable($filePath);
    }

    protected function onlyHeaders(): bool
    {
        $requestMethod = $this->inputs->getInArray('REQUEST_METHOD', [IEntry::SOURCE_SERVER]);
        return !empty($requestMethod) && ('HEAD' == strtoupper(strval(reset($requestMethod))));
    }

    /**
     * @param string[]
     * @link https://www.php.net/manual/en/function.fread.php#84115
     * @link http://tools.ietf.org/id/draft-ietf-http-range-retrieval-00.txt
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Range_requests
     * !! BEWARE !! Chrome wants the whole file even if it's too large -> return only first segment with 206 header
     * @throws FilesException
     * @throws PathsException
     */
    protected function parseRanges(array $filePath): void
    {
        $fileSize = $this->files->size($filePath);
        $range = $this->inputs->getInArray('HTTP_RANGE', [IEntry::SOURCE_SERVER]);
        if (!empty($range)) {
            $line = strval(reset($range));
            list($sizeUnit, $range) = explode('=', $line, 2);
            $this->sizeAdapter = Lib\SizeAdapters\Factory::getAdapter($sizeUnit);
            $this->sizeAdapter->fillFileDetails(
                $fileSize,
                intval(strval(Config::get('File', 'step_by', 16384)))
            );
            $this->sizeAdapter->parseRanges($range);
        } else {
            $this->sizeAdapter = new Lib\SizeAdapters\Bytes();
            $this->sizeAdapter->fillFileDetails(
                $fileSize,
                intval(strval(Config::get('File', 'step_by', 16384)))
            );
        }
    }
}
