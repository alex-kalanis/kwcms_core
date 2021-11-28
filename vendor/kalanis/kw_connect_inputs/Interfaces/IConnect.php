<?php

namespace kalanis\kw_connect_inputs\Interfaces;


use kalanis\kw_connect\core\Interfaces\IConnector;
use kalanis\kw_input\Interfaces\IVariables;


/**
 * Interface IConnect
 * @package kalanis\kw_connect_inputs\Interfaces
 * How to connect data from inputs to filter, sorter and pager
 */
interface IConnect
{
    /**
     * Set configuration which describe relations in inputs
     * @param IConfig $config
     * @return $this
     */
    public function setConfig(IConfig $config): self;

    /**
     * Set inputs itself - what came to the system
     * @param IVariables $inputs
     * @return $this
     */
    public function setInputs(IVariables $inputs): self;

    /**
     * Process all data and fill filter, sorter and pager
     * @return $this
     */
    public function process(): self;

    /**
     * Return updated Connector
     * @return IConnector
     */
    public function getConnector(): IConnector;
}
