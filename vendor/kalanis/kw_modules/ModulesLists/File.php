<?php

namespace kalanis\kw_modules\ModulesLists;


use kalanis\kw_modules\Interfaces\Lists\IModulesList;
use kalanis\kw_modules\Interfaces\Lists\File\IParamFormat;
use kalanis\kw_modules\Interfaces\Lists\IFile;
use kalanis\kw_modules\ModuleException;
use kalanis\kw_paths\Stuff;


/**
 * Class File
 * @package kalanis\kw_modules\ModulesLists
 */
class File implements IModulesList
{
    const PARAM_SEPARATOR = '|';
    const LINE_SEPARATOR = "\r\n";

    /** @var IFile */
    protected $storage = null;
    /** @var IParamFormat */
    protected $format = null;

    public function __construct(IFile $storage, IParamFormat $format)
    {
        $this->storage = $storage;
        $this->format = $format;
    }

    public function setModuleLevel(int $level): void
    {
        $this->storage->setModuleLevel($level);
    }

    public function add(string $moduleName, bool $enabled = false, array $params = []): bool
    {
        $all = $this->listing();
        if (isset($all[$moduleName])) {
            return false;
        }

        $record = new Record();
        $record->setModuleName($moduleName);
        $record->setEnabled($enabled);
        $record->setParams($params);

        $all[$moduleName] = $record;
        return $this->saveData($all);
    }

    public function get(string $moduleName): ?Record
    {
        $all = $this->listing();
        return isset($all[$moduleName]) ? $all[$moduleName] : null;
    }

    public function listing(): array
    {
        $records = $this->unpack($this->storage->load());
        return array_combine(array_map([$this, 'getRecordName'], $records), $records);
    }

    public function getRecordName(Record $record): string
    {
        return $record->getModuleName();
    }

    public function updateBasic(string $moduleName, ?bool $enabled, ?array $params): bool
    {
        if (is_null($enabled) && is_null($params)) {
            return false;
        }
        $all = $this->listing();
        if (!isset($all[$moduleName])) {
            return false;
        }
        /** @var Record $rec */
        $rec = & $all[$moduleName];
        if (!is_null($params)) {
            $rec->setParams($params);
        }
        if (!is_null($enabled)) {
            $rec->setEnabled($enabled);
        }
        return $this->saveData($all);
    }

    public function updateObject(Record $record): bool
    {
        $all = $this->listing();
        if (!isset($all[$record->getModuleName()])) {
            return false;
        }
        // intentionally separated
        /** @var Record $rec */
        $rec = & $all[$record->getModuleName()];
        $rec->setParams($record->getParams());
        $rec->setEnabled($record->isEnabled());
        return $this->saveData($all);
    }

    public function remove(string $moduleName): bool
    {
        $all = $this->listing();
        if (isset($all[$moduleName])) {
            unset($all[$moduleName]);
        }
        return $this->saveData($all);
    }

    /**
     * @param Record[] $records
     * @throws ModuleException
     * @return bool
     */
    protected function saveData(array $records): bool
    {
        return $this->storage->save($this->pack($records));
    }

    /**
     * @param Record[] $records
     * @return string
     */
    public function pack(array $records): string
    {
        return implode(self::LINE_SEPARATOR, array_map([$this, 'toLine'], $records)) . self::LINE_SEPARATOR;
    }

    public function toLine(Record $record): string
    {
        return implode(static::PARAM_SEPARATOR, [
            $record->getModuleName(),
            strval(intval($record->isEnabled())),
            Stuff::arrayIntoHttpString($record->getParams()),
            ''
        ]);
    }

    /**
     * @param mixed $content
     * @return Record[]
     */
    public function unpack($content): array
    {
        return array_map([$this, 'fillRecord'],
            array_filter(explode(static::LINE_SEPARATOR, strval($content)), [$this, 'useLine'])
        );
    }

    public function useLine(string $line): bool
    {
        return !empty($line) && ('#' != $line[0]);
    }

    public function fillRecord(string $line): Record
    {
        list($name, $enabled, $params, ) = explode(static::PARAM_SEPARATOR, $line, 4);
        $record = new Record();
        $record->setModuleName($name);
        $record->setEnabled(boolval(intval(strval($enabled))));
        $record->setParams(Stuff::httpStringIntoArray($params));
        return $record;
    }
}
