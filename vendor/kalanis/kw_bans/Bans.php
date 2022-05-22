<?php

namespace kalanis\kw_bans;


use kalanis\kw_bans\Bans\ABan;


class Bans
{
    /** @var Bans\ABan[] */
    protected $sources = null;

    /**
     * @param mixed ...$sources
     * @throws BanException
     */
    public function __construct(...$sources)
    {
        $factory = new Bans\Factory();
        foreach ($sources as $source) {
            $this->sources[] = $factory->whichType($source);
        }
    }

    /**
     * @param mixed ...$toCompare
     * @return bool
     * @throws BanException
     */
    public function has(...$toCompare): bool
    {
        $smallerSources = array_slice($this->sources, 0, min(count($this->sources), count($toCompare)));
        foreach ($smallerSources as $i => $source) {
            /** @var ABan $source */
            if (is_string($toCompare[$i]) && !empty($toCompare[$i])) {
                $source->setLookedFor($toCompare[$i]);
                if ($source->isBanned()) {
                    return true;
                }
            }
        }
        return false;
    }
}
