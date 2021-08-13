<?php

namespace kalanis\kw_input;


use ArrayAccess;
use kalanis\kw_input\Interfaces\IVariables;
use kalanis\kw_input\Interfaces\IInputs;


/**
 * Class Variables
 * @package kalanis\kw_input
 * Helping class for passing info from inputs into objects
 */
class Variables implements IVariables
{
    /** @var Inputs */
    protected $inputs = null;

    public function __construct(IInputs $inputs)
    {
        $this->inputs = $inputs;
    }

    public function getInObject(string $entryKey = null, array $entrySources = []): ArrayAccess
    {
        return new Input($this->getInArray($entryKey, $entrySources));
    }

    public function getInArray(string $entryKey = null, array $entrySources = []): array
    {
        return $this->intoKeyObjectArray($this->inputs->getIn($entryKey, $entrySources));
    }

    /**
     * @param iterable $entries
     * @return Interfaces\IEntry[]
     */
    protected function intoKeyObjectArray(iterable $entries): array
    {
        $result = [];
        foreach ($entries as $entry) {
            /** @var Interfaces\IEntry $entry */
            $result[$entry->getKey()] = $entry;
        }
        return $result;
    }
}
