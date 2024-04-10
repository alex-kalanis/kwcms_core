<?php

namespace KWCMS\modules\Pedigree\AdminControllers;


use kalanis\kw_accounts\Interfaces\IProcessClasses;
use kalanis\kw_confs\ConfException;
use kalanis\kw_confs\Config;
use kalanis\kw_mapper\MapperException;
use kalanis\kw_mapper\Records\ARecord;
use kalanis\kw_modules\Output;
use kalanis\kw_pedigree\GetEntries;
use kalanis\kw_pedigree\PedigreeException;
use KWCMS\modules\Core\Libs\AAuthModule;
use KWCMS\modules\Pedigree\Lib\TCorrectConnect;


/**
 * Class Lookup
 * @package KWCMS\modules\Pedigree\AdminControllers
 * Site's Pedigree - lookup
 */
class Lookup extends AAuthModule
{
    use TCorrectConnect;

    /** @var MapperException|null */
    protected $error = null;
    /** @var ARecord[] */
    protected array $lookedUp = [];
    /** @var GetEntries */
    protected ?GetEntries $entry = null;

    /**
     * @param mixed ...$constructParams
     * @throws ConfException
     * @throws PedigreeException
     */
    public function __construct(...$constructParams)
    {
        Config::load('Pedigree');
        \kalanis\kw_pedigree\Config::init();
        $this->initTCorrectConnect($constructParams);
    }

    public function allowedAccessClasses(): array
    {
        return [IProcessClasses::CLASS_MAINTAINER, IProcessClasses::CLASS_ADMIN, IProcessClasses::CLASS_USER,];
    }

    public function run(): void
    {
        try {
            $this->entry = new GetEntries($this->getConnectRecord());
            $this->entry->getStorage()->setRecord($this->entry->getRecord());
            $sex = $this->getFromParam('sex');
            $this->lookedUp = $this->entry->getStorage()->getLike(
                strval($this->getFromParam('key')),
                is_null($sex) ? null : strval($sex)
            );
        } catch (PedigreeException | MapperException $ex) {
            $this->error = $ex;
        }
    }

    public function result(): Output\AOutput
    {
        if ($this->error) {
            $out = new Output\JsonError();
            return $out->setContent($this->error->getCode(), $this->error->getMessage());
        } else {
            $out = new Output\Json();
            return $out->setContent(['data' => array_map([$this, 'getItems'], $this->lookedUp)]);
        }
    }

    /**
     * @param ARecord $record
     * @throws MapperException
     * @return array<string, string>
     */
    protected function getItems(ARecord $record): array
    {
        $source = $this->entry->getStorage();
        return [
            'id' => strval($record->offsetGet($source->getIdKey())),
            'short' => strval($record->offsetGet($source->getShortKey())),
            'name' => strval($record->offsetGet($source->getNameKey())),
            'family' => strval($record->offsetGet($source->getFamilyKey())),
        ];
    }
}
