<?php

namespace kalanis\kw_input\Filtered;


use kalanis\kw_input\Interfaces;
use kalanis\kw_input\Traits;


/**
 * Class AFromArrays
 * @package kalanis\kw_input\Filtered
 * Helping class for passing info from simple arrays into objects
 */
abstract class AFromArrays implements Interfaces\IFiltered
{
    use Traits\TFilter;
    use Traits\TKV;

    /** @var Interfaces\IEntry[] */
    protected array $entries = [];

    /**
     * @param Interfaces\IEntry[] $entries
     */
    public function __construct(array $entries)
    {
        $this->entries = $entries;
    }

    public function getInArray(?string $entryKey = null, array $entrySources = []): array
    {
        return $this->keysValues(
            $this->whichKeys(
                $entryKey,
                $this->entries
            )
        );
    }
}
