<?php

namespace KWCMS\modules\Short\Lib;


use kalanis\kw_confs\ConfException;
use kalanis\kw_confs\Config;
use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_files\FilesException;
use kalanis\kw_langs\Lang;
use kalanis\kw_mapper\MapperException;
use kalanis\kw_paths\Path;
use kalanis\kw_paths\PathsException;
use KWCMS\modules\Short\ShortException;


/**
 * Class MessageAdapter
 * @package KWCMS\modules\Short\Lib
 * Connect short messages into system
 */
class MessageAdapter
{
    /** @var CompositeAdapter */
    protected $files = null;
    /** @var Path */
    protected $systemPath = null;
    /** @var ShortMessage */
    protected $record = null;
    /** @var string[] */
    protected $targetPath = [];

    /**
     * @param CompositeAdapter $files
     * @param Path $systemPath
     * @param string[] $targetDir
     * @throws MapperException
     * @throws ConfException
     */
    public function __construct(CompositeAdapter $files, Path $systemPath, array $targetDir)
    {
        Config::load('Short');
        $this->record = new ShortMessage();
        $this->files = $files;
        $this->targetPath = $this->describePath($targetDir);
        $this->systemPath = $systemPath;
    }

    /**
     * @throws FilesException
     * @throws PathsException
     * @throws ShortException
     */
    public function createRecordFile(): void
    {
        if ($this->files->exists($this->targetPath) && $this->files->isFile($this->targetPath)) {
            return;
        }
        if (!$this->files->saveFile($this->targetPath, '')) {
            throw new ShortException(Lang::get('short.cannot_write'));
        }
    }

    /**
     * @throws MapperException
     * @throws FilesException
     * @throws PathsException
     * @throws ShortException
     * @return ShortMessage
     */
    public function getRecord(): ShortMessage
    {
        if ((!$this->files->exists($this->targetPath)) || !$this->files->isFile($this->targetPath)) {
            throw new ShortException(Lang::get('short.cannot_read'));
        }
        $mapper = $this->record->getMapper();
        /** @var ShortMessageMapper $mapper */
        $mapper->setAccessing($this->files);
        $mapper->setCombinedPath($this->targetPath);
        return $this->record;
    }

    /**
     * @param string[] $dirPath
     * @return string[]
     */
    protected function describePath(array $dirPath): array
    {
        return array_merge(array_filter($dirPath), [
            Config::get('Short', 'name', 'index') . Config::get('Short', 'suff', '.short')
        ]);
    }
}
