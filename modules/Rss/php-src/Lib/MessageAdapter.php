<?php

namespace KWCMS\modules\Rss\Lib;


use kalanis\kw_confs\Config;
use kalanis\kw_langs\Lang;
use KWCMS\modules\Rss\RssException;


/**
 * Class MessageAdapter
 * @package KWCMS\modules\Rss\Lib
 * Connect short messages into system
 */
class MessageAdapter
{
    protected $record = null;
    protected $targetDir = null;

    public function __construct(string $targetDir)
    {
        Config::load('Rss');
        $this->record = new ShortMessage();
        $this->targetDir = $targetDir;
    }

    /**
     * @throws RssException
     */
    public function createRecordFile(): void
    {
        $path = $this->describePath($this->targetDir);
        if (file_exists($path)) {
            return;
        }
        if (false === file_put_contents($path, '')) {
            throw new RssException(Lang::get('rss.cannot_write'));
        }
    }

    /**
     * @return ShortMessage
     * @throws RssException
     */
    public function getRecord(): ShortMessage
    {
        $path = realpath($this->describePath($this->targetDir));
        if (false === $path || !is_file($path)) {
            throw new RssException(Lang::get('rss.cannot_read'));
        }
        $mapper = $this->record->getMapper();
        /** @var \kalanis\kw_mapper\Mappers\File\ATable $mapper */
        $mapper->setFormat('\KWCMS\modules\Rss\Lib\SeparatedElements');
        $mapper->setFile($path);
        return $this->record;
    }

    protected function describePath(string $dirPath): string
    {
        return $dirPath . DIRECTORY_SEPARATOR
            . Config::get('Rss', 'name', 'index')
            . Config::get('Rss', 'suff', '.short')
            ;
    }
}
