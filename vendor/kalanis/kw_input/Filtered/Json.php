<?php

namespace kalanis\kw_input\Filtered;


use ArrayAccess;
use kalanis\kw_input\Entries\Entry;
use kalanis\kw_input\Input;
use kalanis\kw_input\InputException;
use kalanis\kw_input\Interfaces;


/**
 * Class Json
 * @package kalanis\kw_input\Filtered
 * Helping class for passing info from json strings into objects
 */
class Json implements Interfaces\IFiltered
{
    /** @var array<string|int, mixed> */
    protected $inputs = null;

    /**
     * @param string $input
     * @throws InputException
     */
    public function __construct(string $input)
    {
        $array = json_decode($input, true);
        if (is_null($array) && (json_last_error())) {
            throw new InputException(json_last_error_msg());
        }
        $this->inputs = (array) $array;
    }

    public function getInObject(?string $entryKey = null, array $entrySources = []): ArrayAccess
    {
        return new Input($this->getInArray($entryKey, $entrySources));
    }

    public function getInArray(?string $entryKey = null, array $entrySources = []): array
    {
        $result = [];
        foreach ($this->inputs as $key => $value) {
            if (is_null($entryKey) || ($key === $entryKey)) {
                $entry = new Entry();
                $entry->setEntry(Entry::SOURCE_JSON, strval($key), $value);
                $result[strval($key)] = $entry;
            }
        }
        return $result;
    }
}
