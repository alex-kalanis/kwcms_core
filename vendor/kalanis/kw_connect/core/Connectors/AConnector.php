<?php

namespace kalanis\kw_connect\core\Connectors;


use kalanis\kw_connect\core\AIterator;
use kalanis\kw_connect\core\Interfaces\IRow;


/**
 * Class AConnector
 * @package kalanis\kw_connect\core\Connectors
 */
abstract class AConnector extends AIterator
{
    protected $filterTypeDirectory = 'FilterType';

    /** @var IRow[] */
    protected $translatedData = [];

    protected function getIterableName(): string
    {
        return 'translatedData';
    }

    abstract protected function parseData(): void;

    /**
     * Get row with data by preset key
     * @param $key
     * @return IRow
     */
    public function getByKey($key): IRow
    {
        return $this->translatedData[$key];
    }
}
