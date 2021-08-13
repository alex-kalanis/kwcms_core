<?php

namespace kalanis\kw_bans\Bans;


use kalanis\kw_bans\Sources\ASources;


abstract class ABan
{
    /** @var string[] */
    protected $foundRecords = [];

    abstract public function __construct(ASources $source);

    abstract public function setLookedFor(string $lookedFor): void;

    public function isBanned(): bool
    {
        $this->compare();
        return !empty($this->foundRecords);
    }

    abstract protected function compare(): void;
}
