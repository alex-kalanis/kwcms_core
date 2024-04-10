<?php

namespace kalanis\kw_input\Filtered;


use kalanis\kw_input\Entries\Entry;
use kalanis\kw_input\InputException;
use kalanis\kw_input\Interfaces;
use kalanis\kw_input\Traits;


/**
 * Class Json
 * @package kalanis\kw_input\Filtered
 * Helping class for passing info from json strings into objects
 */
class Json implements Interfaces\IFiltered
{
    use Traits\TFill;
    use Traits\TFilter;
    use Traits\TKV;

    /** @var array<string|int, mixed|null> */
    protected array $inputs = [];

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

    public function getInArray(?string $entryKey = null, array $entrySources = []): array
    {
        return $this->keysValues(
            $this->whichKeys(
                $entryKey,
                $this->fillFromEntries(Entry::SOURCE_JSON, $this->inputs)
            )
        );
    }
}
