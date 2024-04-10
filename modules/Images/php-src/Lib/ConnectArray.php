<?php

namespace KWCMS\modules\Images\Lib;


use kalanis\kw_connect\arrays\Connector;
use kalanis\kw_connect\core\Interfaces\IRow;
use kalanis\kw_files\FilesException;
use kalanis\kw_paths\PathsException;
use kalanis\kw_tree\Essentials\FileNode;
use KWCMS\modules\Images\Interfaces\IProcessFiles;


/**
 * Class ConnectArray
 * @package KWCMS\modules\Images\Lib
 * Mapper is array of connecting items.
 */
class ConnectArray extends Connector
{
    /** @var string[] */
    protected array $whereDir = [];
    protected IProcessFiles $libFiles;

    public function __construct(array $source, array $whereDir, IProcessFiles $libFiles)
    {
        parent::__construct($source);
        $this->whereDir = $whereDir;
        $this->libFiles = $libFiles;
    }

    /**
     * @param FileNode $data
     * @throws FilesException
     * @throws PathsException
     * @return IRow
     */
    public function getTranslated($data): IRow
    {
        return new ConnectItem($data, $this->whereDir, $this->libFiles);
    }
}
