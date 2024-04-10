<?php

namespace kalanis\kw_bans\Bans;


use kalanis\kw_bans\Interfaces\IKBTranslations;
use kalanis\kw_bans\Sources\ASources;


class Basic extends ABan
{
    protected ASources $source;
    protected string $searchKey = '';

    public function __construct(ASources $source, ?IKBTranslations $lang = null)
    {
        $this->source = $source;
    }

    public function setLookedFor(string $lookedFor): void
    {
        $this->searchKey = $lookedFor;
    }

    protected function matched(): array
    {
        // compare string with array of all banned strings
        $s = mb_strtolower($this->searchKey);
        $foundRecords = [];
        foreach ($this->source->getRecords() as $index => $word) {
            $found = mb_strpos($s, mb_strtolower($word));
            if (false !== $found) {
                $foundRecords[$index] = intval($found);
            }
        }
        return $foundRecords;
    }
}
