<?php

namespace kalanis\kw_files\Processing\Storage\Nodes;


use kalanis\kw_files\Interfaces\IProcessNodes;
use kalanis\kw_files\Traits\TLang;
use kalanis\kw_paths\Extras\TPathTransform;


/**
 * Class ANodes
 * @package kalanis\kw_files\Processing\Storage\Nodes
 * Process nodes in storages - deffer when you can access them directly or must be a middleman there
 */
abstract class ANodes implements IProcessNodes
{
    use TLang;
    use TPathTransform;

    protected function getStorageSeparator(): string
    {
        return DIRECTORY_SEPARATOR;
    }
}
