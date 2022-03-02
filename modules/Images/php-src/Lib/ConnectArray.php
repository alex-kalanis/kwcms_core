<?php

namespace KWCMS\modules\Images\Lib;


use kalanis\kw_images\Files;
use kalanis\kw_connect\arrays\Connector;
use kalanis\kw_connect\core\Interfaces\IRow;


/**
 * Class ConnectArray
 * @package KWCMS\modules\Images\Lib
 * Mapper is array of connecting items.
 */
class ConnectArray extends Connector
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
