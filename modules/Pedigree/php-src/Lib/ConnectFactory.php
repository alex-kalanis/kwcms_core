<?php

namespace KWCMS\modules\Pedigree\Lib;


use kalanis\kw_pedigree\PedigreeException;
use kalanis\kw_pedigree\Storage;


/**
 * Class ConnectFactory
 * @package KWCMS\modules\Short\Lib
 */
class ConnectFactory
{
    protected $map = [
        'volume' => Storage\File\PedigreeRecord::class,
        'file' => Storage\File\PedigreeRecord::class,
        'table' => Storage\SingleTable\PedigreeRecord::class,
        'single' => Storage\SingleTable\PedigreeRecord::class,
        'single_table' => Storage\SingleTable\PedigreeRecord::class,
        'tables' => Storage\MultiTable\PedigreeItemRecord::class,
        'multiple' => Storage\MultiTable\PedigreeItemRecord::class,
        'multiple_tables' => Storage\MultiTable\PedigreeItemRecord::class,
    ];

    /**
     * @param mixed $constructParams
     * @throws PedigreeException
     * @return Storage\APedigreeRecord
     */
    public function getByConf($constructParams): Storage\APedigreeRecord
    {
        if (isset($constructParams['module_pedigree'])) {
            $key = strval($constructParams['module_pedigree']);
            if (isset($this->map[$key])) {
                try {
                    $ref = new \ReflectionClass($this->map[$key]);
                } catch (\ReflectionException $ex) {
                    throw new PedigreeException(sprintf('Cannot init the key *%s*', $key));
                }
                $instance = $ref->newInstance();
                if ($instance instanceof Storage\APedigreeRecord) {
                    return $instance;
                }
                throw new PedigreeException(sprintf('The class under key *%s* is not an instance of *APedigreeRecord*', $key));
            }
            throw new PedigreeException(sprintf('The key *%s* is not in class map', $key));
        }
        throw new PedigreeException('No configuration passed.');
    }
}
