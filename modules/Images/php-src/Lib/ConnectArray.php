<?php

namespace KWCMS\modules\Images\Lib;


use kalanis\kw_connect\arrays\Connector;
use kalanis\kw_connect\core\Interfaces\IRow;
use kalanis\kw_files\FilesException;
use kalanis\kw_tree\FileNode;
use KWCMS\modules\Images\Interfaces\IProcessFiles;


/**
 * Class ConnectArray
 * @package KWCMS\modules\Images\Lib
 * Mapper is array of connecting items.
 */
class ConnectArray extends Connector
{
    /** @var string */
    protected $whereDir = '';
    /*** @var IProcessFiles */
    protected $libFiles = null;

    public function __construct(array $source, string $whereDir, IProcessFiles $libFiles)
    {
        parent::__construct($source);
        $this->whereDir = $whereDir;
        $this->libFiles = $libFiles;
    }

    /**
     * @param FileNode $data
     * @throws FilesException
     * @return IRow
     */
    public function getTranslated($data): IRow
    {
        return new ConnectItem($data, $this->whereDir, $this->libFiles);
    }
}
