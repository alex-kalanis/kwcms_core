<?php

namespace KWCMS\modules\Pedigree\Lib;


use kalanis\kw_pedigree\PedigreeException;
use kalanis\kw_pedigree\Storage\APedigreeRecord;


/**
 * Trait TCorrectConnect
 * @package KWCMS\modules\Short\Lib
 */
trait TCorrectConnect
{
    /** @var APedigreeRecord|null */
    protected $connectViaRecord = null;

    /**
     * @param mixed $constructParams
     * @throws PedigreeException
     */
    protected function initTCorrectConnect($constructParams): void
    {
        $this->connectViaRecord = (new ConnectFactory())->getByConf($constructParams);
    }

    /**
     * @throws PedigreeException
     * @return APedigreeRecord
     */
    protected function getConnectRecord(): APedigreeRecord
    {
        if (empty($this->connectViaRecord)) {
            throw new PedigreeException('Connection to pedigree must be initialized');
        }
        return $this->connectViaRecord;
    }
}
