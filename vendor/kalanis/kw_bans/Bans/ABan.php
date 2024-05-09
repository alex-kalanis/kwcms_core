<?php

namespace kalanis\kw_bans\Bans;


use kalanis\kw_bans\BanException;
use kalanis\kw_bans\Interfaces\IKBTranslations;
use kalanis\kw_bans\Ip;
use kalanis\kw_bans\Sources\ASources;


abstract class ABan
{
    abstract public function __construct(ASources $source, ?IKBTranslations $lang = null);

    /**
     * @param string $lookedFor
     * @throws BanException
     */
    abstract public function setLookedFor(string $lookedFor): void;

    public function isBanned(): bool
    {
        return !empty($this->matched());
    }

    /**
     * @return array<int, string|int|Ip>
     */
    abstract protected function matched(): array;
}
