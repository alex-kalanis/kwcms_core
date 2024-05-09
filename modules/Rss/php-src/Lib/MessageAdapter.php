<?php

namespace KWCMS\modules\Rss\Lib;


use kalanis\kw_confs\ConfException;
use kalanis\kw_confs\Config;
use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_files\FilesException;
use kalanis\kw_langs\Lang;
use kalanis\kw_mapper\MapperException;
use kalanis\kw_paths\Path;
use kalanis\kw_paths\PathsException;
use KWCMS\modules\Rss\RssException;


/**
 * Class MessageAdapter
 * @package KWCMS\modules\Rss\Lib
 * Connect short messages into system
 */
class MessageAdapter
{
    protected CompositeAdapter $files;
    protected ShortMessage $record;
    /** @var string[] */
    protected array $targetPath = [];

    /**
     * @param CompositeAdapter $files
     * @param string[] $targetDir
     * @throws MapperException
     * @throws ConfException
     */
    public function __construct(CompositeAdapter $files, array $targetDir)
    {
        Config::load('Rss');
        $this->record = new ShortMessage();
        $this->files = $files;
        $this->targetPath = $this->describePath($targetDir);
    }

    /**
     * @throws MapperException
     * @throws FilesException
     * @throws PathsException
     * @throws RssException
     * @return ShortMessage
     */
    public function getRecord(): ShortMessage
    {
        if ((!$this->files->exists($this->targetPath)) || !$this->files->isFile($this->targetPath)) {
            throw new RssException(Lang::get('short.cannot_read'));
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
            Config::get('Rss', 'name', 'index') . Config::get('Rss', 'suff', '.short')
        ]);
    }
}
