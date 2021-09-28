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
 */
class File extends AModule
{
    protected $mime = null;
    protected $extLink = null;
    protected $intLink = null;

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
        if (intval(strval(Config::get('File', 'max_size', 16777216))) < filesize($filePath)) {
            Headers::setCustomCode(strval(reset($protocol)), 413);
            return $out;
        }
        $content = @file_get_contents($filePath);
        if (!$content) {
            Headers::setCustomCode(strval(reset($protocol)), 417);
            return $out;
        }

        // mime type
        header("Content-Type: " . $this->mime->mimeByPath($filePath));

        // pass name
        $name = Stuff::filename($filePath);
        header('Content-Disposition: filename="' . $name . '"');

        // download flag
        $down = $this->inputs->getInArray('download');
        if (!empty($down) && boolval(intval(strval(reset($down))))) {
            header('Content-Disposition: attachment; filename="' . $name . '"');
        }
        return $out->setContent($content);
    }
}
