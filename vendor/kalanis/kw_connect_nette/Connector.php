<?php

namespace kalanis\kw_connect_nette;


use kalanis\kw_connect\Connectors\AConnector;
use kalanis\kw_connect\Interfaces\IConnector;
use kalanis\kw_connect\Interfaces\IFilterFactory;
use kalanis\kw_connect\Interfaces\IFilterType;
use kalanis\kw_connect\Interfaces\IRow;
use Nette\Database\IRow as NetteRow;
use Nette\Database\Table\Selection;


/**
 * Class Connector
 * @package kalanis\kw_connect_nette
 * Datasource is Nette\Database
 */
class Connector extends AConnector implements IConnector
{
    /** @var Selection */
    protected $netteTable;
    /** @var string */
    protected $primaryKey;
    /** @var array */
    protected $sorters;
    /** @var int */
    protected $limit;
    /** @var int */
    protected $offset;
    /** @var bool */
    protected $dataFetched = false;

    public function __construct(Selection $netteTable, string $primaryKey)
    {
        $this->netteTable = $netteTable;
        $this->primaryKey = $primaryKey;
    }

    public function setFiltering(string $colName, string $value, IFilterType $type): void
    {
        $type->setDataSource($this->netteTable);
        $type->setFiltering($colName, $value);
    }

    public function setSorting(string $colName, string $direction): void
    {
        $this->sorters[] = [$colName, $direction];
    }

    public function setPagination(?int $offset, ?int $limit): void
    {
        $this->offset = $offset;
        $this->limit = $limit;
    }

    public function getTotalCount(): int
    {
        return $this->netteTable->count('*');
    }

    public function fetchData(): void
    {
        $orders = [];
        foreach ($this->sorters as list($colName, $direction)) {
            $orders[] = strval($colName) . ' ' . strval($direction);
        }
        $this->netteTable->order($orders);
        $this->netteTable->limit($this->limit, $this->offset);
        $this->parseData();
    }

    protected function parseData(): void
    {
        foreach ($this->netteTable->fetchAll() as $mapper) {
            $this->translatedData[$this->getPrimaryKey($mapper)] = $this->getTranslated($mapper);
        }
    }

    protected function getTranslated(NetteRow $data): IRow
    {
        return new Row($data);
    }

    protected function getPrimaryKey(NetteRow $record): string
    {
        return $record->offsetGet($this->primaryKey);
    }

    public function getFilterFactory(): IFilterFactory
    {
        return Filters\Factory::getInstance();
    }
}