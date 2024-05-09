<?php

namespace kalanis\kw_tree_controls;


use ArrayAccess;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_input\Interfaces\IFiltered;


/**
 * Trait TWhereDir
 * @package kalanis\kw_tree_controls
 * Trait to controllers to access dirs from session
 */
trait TWhereDir
{
    protected string $whereConst = 'dir';
    protected ?ArrayAccess $storeWhere = null;
    protected ?IFiltered $anotherSource = null;

    public function initWhereDir(ArrayAccess $storeWhere, ?IFiltered $inputs): void
    {
        $this->storeWhere = $storeWhere;
        $this->anotherSource = $inputs;
    }

    /**
     * @param string $where
     * @throws FormsException
     */
    public function updateWhereDir(string $where): void
    {
        if ($this->storeWhere) {
            $this->getStoreWhere()->offsetSet($this->whereConst, $where);
        }
    }

    /**
     * @throws FormsException
     * @return string
     */
    public function getWhereDir(): string
    {
        if ($this->storeWhere && $this->getStoreWhere()->offsetExists($this->whereConst)) {
            return $this->getStoreWhere()->offsetGet($this->whereConst);
        }
        if ($this->anotherSource) {
            $dirs = $this->getFilteredSource()->getInArray($this->whereConst);
            return strval(reset($dirs));
        }
        return '';
    }

    /**
     * @throws FormsException
     * @return ArrayAccess
     */
    protected function getStoreWhere(): ArrayAccess
    {
        if (empty($this->storeWhere)) {
            throw new FormsException('Set first where to store target data');
        }
        return $this->storeWhere;
    }

    /**
     * @throws FormsException
     * @return IFiltered
     */
    protected function getFilteredSource(): IFiltered
    {
        if (empty($this->anotherSource)) {
            throw new FormsException('Set first where to get data from another source');
        }
        return $this->anotherSource;
    }
}
