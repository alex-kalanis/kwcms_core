<?php

namespace KWCMS\modules\Images\Lib;


use kalanis\kw_connect\core\Interfaces\IRow;
use kalanis\kw_files\FilesException;
use kalanis\kw_tree\FileNode;
use KWCMS\modules\Images\Interfaces\IProcessFiles;


/**
 * Class ConnectItem
 * @package KWCMS\modules\Images\Lib
 * Connect single image to table
 */
class ConnectItem implements IRow
{
    protected $array;

    /**
     * @param FileNode $item
     * @param string $whereDir
     * @param IProcessFiles $libFiles
     * @throws FilesException
     */
    public function __construct(FileNode $item, string $whereDir, IProcessFiles $libFiles)
    {
        $this->array = [
            'name' => $item->getName(),
            'dir' => $item->getDir(),
            'size' => $item->getSize(),
            'desc' => $libFiles->readDesc($whereDir . DIRECTORY_SEPARATOR . $item->getPath()),
            'thumb' => $libFiles->reverseThumb($whereDir . DIRECTORY_SEPARATOR . $item->getPath()),
        ];
    }

    public function getValue($property)
    {
        return $this->array[$property];
    }

    public function __isset($name)
    {
        return isset($this->array[$name]);
    }
}
