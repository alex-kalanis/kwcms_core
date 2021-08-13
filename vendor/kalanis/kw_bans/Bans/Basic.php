<?php

namespace kalanis\kw_bans\Bans;


use kalanis\kw_bans\Sources\ASources;


class Basic extends ABan
{
    /** @var string[] */
    protected $knownRecords = [];
    /** @var string */
    protected $searchKey = '';

    public function __construct(ASources $source)
    {
        $this->knownRecords = $source->getRecords();
    }

    public function setLookedFor(string $lookedFor): void
    {
        $this->searchKey = $lookedFor;
    }

    protected function compare(): void
    {
        // compare string with array of all banned strings
        $s = mb_strtolower($this->searchKey);
        $this->foundRecords = [];
        foreach ($this->knownRecords as $index => $word) {
            $found = mb_strpos($s, mb_strtolower($word));
            if (false !== $found) {
                $this->foundRecords[$index] = $found;
            }
        }
    }
}
