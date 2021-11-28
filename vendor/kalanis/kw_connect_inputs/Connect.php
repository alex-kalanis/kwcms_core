<?php

namespace kalanis\kw_connect_inputs;


use kalanis\kw_connect\core\ConnectException;
use kalanis\kw_connect\core\Interfaces\IConnector;
use kalanis\kw_connect_inputs\Interfaces;
use kalanis\kw_filter\Interfaces\IFilter;
use kalanis\kw_input\Interfaces\IVariables;
use kalanis\kw_pager\Interfaces\IPager;
use kalanis\kw_sorter\Interfaces\ISorter;
use Traversable;


/**
 * Class Connect
 * @package kalanis\kw_connect_inputs
 * Connections between inputs and params for queries
 * @todo: It should behave like Table - define columns and if it's possible to filter them and sort them
 */
class Connect implements Interfaces\IConnect
{
    /** @var Interfaces\IConfig|null */
    protected $config = null;
    /** @var IConnector|null */
    protected $connector = null;
    /** @var IFilter|null */
    protected $filter = null;
    /** @var ISorter|null */
    protected $sorter = null;
    /** @var IPager|null */
    protected $pager = null;
    /** @var IVariables|null */
    protected $inputs = null;

    public function __construct(IConnector $connector, IFilter $filter, ISorter $sorter, IPager $pager)
    {
        $this->connector = $connector;
        $this->filter = $filter;
        $this->sorter = $sorter;
        $this->pager = $pager;
    }

    public function setConfig(Interfaces\IConfig $config): Interfaces\IConnect
    {
        $this->config = $config;
        return $this;
    }

    public function setInputs(IVariables $inputs): Interfaces\IConnect
    {
        $this->inputs = $inputs;
        return $this;
    }

    /**
     * @return $this
     * @throws ConnectException
     */
    public function process(): Interfaces\IConnect
    {
        if (empty($this->config)) {
            throw new ConnectException('No config transcription what to set to what.');
        }
        if (empty($this->inputs)) {
            throw new ConnectException('No inputs for transcribe.');
        }
        $this->fillFilter();
        $this->fillSorter();
        $this->fillPager();
        $this->connector->fetchData();
        return $this;
    }

    /**
     * @throws ConnectException
     */
    protected function fillFilter(): void
    {
        $config = $this->config->getFilterEntries();
        $entries = $this->inputs->getInArray( null, [$config->getSource()] );
        $availableKeys = $this->combineTarget($config->getEntries());
        foreach ($entries as $key => $entry) {
            if (isset($availableKeys[$key])) {
                $available = $availableKeys[$key];
                $filter = $this->filter->getDefaultItem();
                $filter->setKey($key)->setValue($entry->getValue());
                if (isset($entries[$available->getLimitationKey()])) {
                    $filter->setRelation($entries[$available->getLimitationKey()]->getValue());
                } else {
                    $filter->setRelation($available->getDefaultLimitation());
                }
                $this->connector->setFiltering($filter->getKey(), $filter->getValue(), $this->filter->getType($filter->getRelation()));
            }
        }
    }

    /**
     * @throws ConnectException
     */
    protected function fillSorter(): void
    {
        $config = $this->config->getSorterEntries();
        $entries = $this->inputs->getInArray( null, [$config->getSource()] );
        $availableKeys = $this->combineTarget($config->getEntries());
        foreach ($entries as $key => $entry) {
            if (isset($availableKeys[$entry->getKey()])) {
                $available = $availableKeys[$entry->getKey()];
                $sorter = $this->sorter->getDefaultItem();
                $sorter->setKey($entry->getKey());
                if (isset($entries[$available->getLimitationKey()])) {
                    $sorter->setDirection($entries[$available->getLimitationKey()]->getValue());
                } else {
                    $sorter->setDirection($available->getDefaultLimitation());
                }
                $this->connector->setSorting($sorter->getKey(), $sorter->getDirection());
            }
        }
    }

    /**
     * @throws ConnectException
     */
    protected function fillPager(): void
    {
        $config = $this->config->getPagerEntries();
        $entries = $this->inputs->getInArray( null, [$config->getSource()] );
        if (isset($entries[$config->getKey()])) {
            $this->pager->setActualPage(intval($entries[$config->getKey()]->getValue()));
            if (isset($entries[$config->getLimitationKey()])) {
                $this->pager->setLimit(intval($entries[$config->getLimitationKey()]->getValue()));
            }
            $this->connector->setPagination($this->pager->getOffset(), $this->pager->getLimit());
        }
    }

    /**
     * @param Traversable $entries
     * @return Interfaces\IEntry[]
     */
    protected function combineTarget(Traversable $entries): array
    {
        $result = [];
        foreach ($entries as $entry) {
            if ($entry instanceof Interfaces\IEntry) {
                $result[$entry->getKey()] = $entry;
            }
        }
        return $result;
    }

    public function getConnector(): IConnector
    {
        return $this->connector;
    }
}
