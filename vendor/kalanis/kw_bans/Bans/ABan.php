<?php

namespace kalanis\kw_bans\Bans;


use kalanis\kw_bans\BanException;
use kalanis\kw_bans\Interfaces\IKBTranslations;
use kalanis\kw_bans\Ip;
use kalanis\kw_bans\Sources\ASources;


abstract class ABan
{
    /** @var IKBTranslations|null */
    protected $lang = null;
    /** @var array<int, string|int|Ip> */
    protected $foundRecords = [];

    abstract public function __construct(ASources $source, ?IKBTranslations $lang = null);

    /**
     * @param string $lookedFor
     * @throws BanException
     */
    abstract public function setLookedFor(string $lookedFor): void;

    public function isBanned(): bool
    {
        $this->compare();
        return !empty($this->foundRecords);
    }

    abstract protected function compare(): void;
}
