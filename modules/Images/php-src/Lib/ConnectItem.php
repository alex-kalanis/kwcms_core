<?php

namespace KWCMS\modules\Images\Lib;


use kalanis\kw_connect\core\Interfaces\IRow;
use kalanis\kw_files\FilesException;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\PathsException;
use kalanis\kw_tree\Essentials\FileNode;
use KWCMS\modules\Images\Interfaces\IProcessFiles;


/**
 * Class ConnectItem
 * @package KWCMS\modules\Images\Lib
 * Connect single image to table
 */
class ConnectItem implements IRow
{
    protected array $array;

    /**
     * @param FileNode $item
     * @param string[] $whereDir
     * @param IProcessFiles $libFiles
     * @throws FilesException
     * @throws PathsException
     */
    public function __construct(FileNode $item, array $whereDir, IProcessFiles $libFiles)
    {
        $pats = new ArrayPath();
        $pats->setArray(array_merge($whereDir, $item->getPath()));
        $this->array = [
            'name' => $pats->getFileName(),
            'dir' => $pats->getStringDirectory(),
            'size' => $item->getSize(),
            'desc' => $libFiles->readDesc($pats->getFileName()),
            'thumb' => $libFiles->reverseThumb($pats->getFileName()),
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
