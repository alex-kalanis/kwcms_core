<?php

namespace kalanis\kw_input\Filtered;


use kalanis\kw_input\Interfaces;
use kalanis\kw_input\Traits;


/**
 * Class EntryArrays
 * @package kalanis\kw_input\Filtered
 * Helping class for passing info from entry arrays into objects
 */
class EntryArrays implements Interfaces\IFiltered
{
    use Traits\TFilter;
    use Traits\TKV;

    /** @var array<int|string, Interfaces\IEntry> */
    protected array $inputs = [];

    /**
     * @param array<int|string, Interfaces\IEntry> $inputs
     */
    public function __construct(array $inputs)
    {
        $this->inputs = $inputs;
    }

    public function getInArray(?string $entryKey = null, array $entrySources = []): array
    {
        return $this->keysValues(
            $this->whichSource(
                $entrySources,
                $this->whichKeys(
                    $entryKey,
                    $this->inputs
                )
            )
        );
    }
}
