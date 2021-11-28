<?php

namespace kalanis\kw_table\core\Interfaces\Form;


use kalanis\kw_connect\ConnectException;
use kalanis\kw_connect\Interfaces\IConnector;
use kalanis\kw_connect\Interfaces\IFilterType;


/**
 * Interface IField
 * @package kalanis\kw_table\core\Interfaces\Form
 * Single entry field in filter form
 */
interface IField
{
    /**
     * Alias of input
     * @param string $alias
     */
    public function setAlias(string $alias): void;

    /**
     * Add form input
     */
    public function add(): void;

    /**
     * CSS styles for each input
     * @param array $attributes
     */
    public function setAttributes(array $attributes): void;

    /**
     * From which source it will read values
     * @param IConnector $dataSource
     */
    public function setDataSourceConnector(IConnector $dataSource): void;

    /**
     * Get filter which will modify results
     * @return IFilterType
     * @throws ConnectException
     */
    public function getFilterType(): IFilterType;
}