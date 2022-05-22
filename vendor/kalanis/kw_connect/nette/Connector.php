<?php

namespace kalanis\kw_connect\nette;


use kalanis\kw_connect\core\AConnector;
use kalanis\kw_connect\core\Interfaces\IConnector;
use kalanis\kw_connect\core\Interfaces\IFilterFactory;
use kalanis\kw_connect\core\Interfaces\IFilterSubs;
use kalanis\kw_connect\core\Interfaces\IOrder;
use kalanis\kw_connect\core\Interfaces\IRow;
use Nette\Database\IRow as NetteRow;
use Nette\Database\Table\Selection;


/**
 * Class Connector
 * @package kalanis\kw_connect\nette
 * Data source is Nette\Database
 */
class Connector extends AConnector implements IConnector
{
    /** @var Selection */
    protected $netteTable;
    /** @var string */
    protected $primaryKey;
    /** @var array */
    protected $ordering;
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

    public function setFiltering(string $colName, string $filterType, $value): void
    {
        $type = $this->getFilterFactory()->getFilter($filterType);
        if ($type instanceof IFilterSubs) {
            $type->addFilterFactory($this->getFilterFactory());
        }
        $type->setDataSource($this->netteTable);
        $type->setFiltering($colName, $value);
    }

    public function setOrdering(string $colName, string $direction): void
    {
        $this->ordering[] = [$colName, $direction];
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
        foreach ($this->ordering as list($colName, $direction)) {
            $dir = IOrder::ORDER_ASC == $direction ? 'ASC' : 'DESC' ;
            $orders[] = strval($colName) . ' ' . $dir;
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
