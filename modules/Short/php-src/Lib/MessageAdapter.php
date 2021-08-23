<?php

namespace KWCMS\modules\Short\Lib;


use kalanis\kw_confs\Config;
use kalanis\kw_input\Interfaces\IVariables;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_langs\Lang;
use kalanis\kw_paths\Path;
use kalanis\kw_tree\TWhereDir;
use KWCMS\modules\Short\ShortException;


/**
 * Class MessageAdapter
 * @package KWCMS\modules\Short\Lib
 * Connect short messages into system
 */
class MessageAdapter
{
    use TWhereDir;

    protected $record = null;
    protected $targetDir = null;

    public function __construct(IVariables $inputs, Path $path)
    {
        Config::load('Short');
        $this->initWhereDir(new SessionAdapter(), $inputs);
        $this->record = new ShortMessage();
        $this->targetDir = $path->getDocumentRoot() . $path->getPathToSystemRoot() . $this->getWhereDir();
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
     * @return ShortMessage
     * @throws ShortException
     */
    public function getRecord(): ShortMessage
    {
        $path = realpath($this->describePath($this->targetDir));
        if (false === $path || !is_file($path)) {
            throw new ShortException(Lang::get('short.cannot_read'));
        }
        $mapper = $this->record->getMapper();
        /** @var \kalanis\kw_mapper\Mappers\File\ATable $mapper */
        $mapper->setFormat('\KWCMS\modules\Short\Lib\SeparatedElements');
        $mapper->setFile($path);
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
