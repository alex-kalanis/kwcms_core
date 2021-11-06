<?php

namespace KWCMS\modules\Images\Lib;


use kalanis\kw_images\Files;
use kalanis\kw_table\Connector\Sources\Arrays;
use kalanis\kw_table\Interfaces\Table\IRow;


/**
 * Class SourceItem
 * @package KWCMS\modules\Images\Lib
 * Mapper is array of connecting items.
 */
class SourceItem extends Arrays
{
    protected $whereDir = '';
    protected $libGallery = null;

    public function __construct(array $source, string $whereDir, Files $libGallery)
    {
        parent::__construct($source);
        $this->whereDir = $whereDir;
        $this->libGallery = $libGallery;
    }

    public function getTranslated($data): IRow
    {
        return new ConnectItem($data, $this->whereDir, $this->libGallery);
    }
}
