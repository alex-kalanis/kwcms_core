<?php

namespace kalanis\kw_modules\Processing;


use kalanis\kw_modules\ModuleException;
use kalanis\kw_modules\Interfaces\IModuleProcessor;
use kalanis\kw_modules\Interfaces\IModuleRecord;
use kalanis\kw_paths\Interfaces\IPaths;
use kalanis\kw_paths\Stuff;


/**
 * Class FileProcessor
 * @package kalanis\kw_modules\Processing
 */
class FileProcessor implements IModuleProcessor
{
    const SEPARATOR = '|';

    /** @var string */
    protected $moduleConfPath = '';
    /** @var IModuleRecord */
    protected $baseRecord = null;
    /** @var string */
    protected $fileName = '';
    /** @var IModuleRecord[] */
    protected $records = [];

    public function __construct(IModuleRecord $baseRecord, string $moduleConfPath)
    {
        $this->moduleConfPath = $moduleConfPath;
        $this->baseRecord = $baseRecord;
    }

    public function setModuleLevel(int $level): void
    {
        $this->records = [];
        $this->fileName = Stuff::sanitize(implode(DIRECTORY_SEPARATOR, [
            $this->moduleConfPath,
            IPaths::DIR_MODULE,
            sprintf('%s.%d.%s', IPaths::DIR_MODULE, $level, IPaths::DIR_CONF )
        ]));
    }

    public function add(string $moduleName): void
    {
        $this->loadOnDemand();
        if (!isset($this->records[$moduleName])) {
            $record = clone $this->baseRecord;
            $record->setModuleName($moduleName);
            $this->records[$moduleName] = $record;
        }
    }

    public function get(string $moduleName): ?IModuleRecord
    {
        $this->loadOnDemand();
        return $this->records[$moduleName] ?? null ;
    }

    public function listing(): array
    {
        $this->loadOnDemand();
        return array_keys($this->records);
    }

    public function update(string $moduleName, string $params): void
    {
        $this->loadOnDemand();
        if (isset($this->records[$moduleName])) {
            $this->records[$moduleName]->updateParams($params);
        }
    }

    public function remove(string $moduleName): void
    {
        $this->loadOnDemand();
        if (isset($this->records[$moduleName])) {
            unset($this->records[$moduleName]);
        }
    }

    /**
     * @throws ModuleException
     */
    protected function loadOnDemand(): void
    {
        if (empty($this->fileName)) {
            throw new ModuleException('Site part and then file name is not set!');
        }
        if (empty($this->records)) {
            $this->load();
        }
    }

    protected function load(): void
    {
        $lines = file($this->fileName);
        if (false !== $lines) {
            foreach ($lines as $line) {
                if (empty($line) || ('#' == $line[0])) {
                    continue;
                }
                list($name, $params, $rest) = explode(static::SEPARATOR, $line, 3);
                $record = clone $this->baseRecord;
                $record->setModuleName($name);
                $record->updateParams($params);
                $this->records[$name] = $record;
            }
        }
    }

    public function save(): void
    {
        $lines = '';
        foreach ($this->records as $record) {
            $lines .= implode(static::SEPARATOR, [$record->getModuleName(), $record->getParams(), '']). "\r\n";
        }
        $result = file_put_contents($this->fileName, $lines);
        if (false === $result) {
            throw new ModuleException('Cannot save module file');
        }
    }
}
