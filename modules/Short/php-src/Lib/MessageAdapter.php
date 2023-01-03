<?php

namespace KWCMS\modules\Short\Lib;


use kalanis\kw_confs\Config;
use kalanis\kw_langs\Lang;
use kalanis\kw_mapper\MapperException;
use kalanis\kw_mapper\Mappers\File\ATable;
use KWCMS\modules\Short\ShortException;


/**
 * Class MessageAdapter
 * @package KWCMS\modules\Short\Lib
 * Connect short messages into system
 */
class MessageAdapter
{
    protected $record = null;
    protected $targetDir = null;

    public function __construct(string $targetDir)
    {
        Config::load('Short');
        $this->record = new ShortMessage();
        $this->targetDir = $targetDir;
    }

    /**
     * @throws ShortException
     */
    public function createRecordFile(): void
    {
        $path = $this->describePath($this->targetDir);
        if (file_exists($path)) {
            return;
        }
        if (false === file_put_contents($path, '')) {
            throw new ShortException(Lang::get('short.cannot_write'));
        }
    }

    /**
     * @throws MapperException
     * @throws ShortException
     * @return ShortMessage
     */
    public function getRecord(): ShortMessage
    {
        $path = realpath($this->describePath($this->targetDir));
        if (false === $path || !is_file($path)) {
            throw new ShortException(Lang::get('short.cannot_read'));
        }
        $mapper = $this->record->getMapper();
        /** @var ATable $mapper */
        $mapper->setFormat(SeparatedElements::class);
        $mapper->setSource($path);
        return $this->record;
    }

    protected function describePath(string $dirPath): string
    {
        return $dirPath . DIRECTORY_SEPARATOR
            . Config::get('Short', 'name', 'index')
            . Config::get('Short', 'suff', '.short')
            ;
    }
}
