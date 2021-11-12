<?php

namespace kalanis\kw_connect\Connectors;


use kalanis\kw_connect\AIterator;
use kalanis\kw_connect\Interfaces\IRow;


/**
 * Class AConnector
 * @package kalanis\kw_connect\Connectors
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
