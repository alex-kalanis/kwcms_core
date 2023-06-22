<?php

namespace kalanis\kw_bans;


use kalanis\kw_bans\Bans\ABan;
use kalanis\kw_bans\Interfaces\IKBTranslations;


/**
 * Class Bans
 * @package kalanis\kw_bans
 * One of main libraries to process detection if that querying system is on internal lists
 */
class Bans
{
    /** @var Bans\ABan[] */
    protected $sources = null;

    /**
     * @param IKBTranslations|null $lang
     * @param string|array<string>|array<int, string>|Sources\ASources $sources
     * @throws BanException
     */
    public function __construct(?IKBTranslations $lang = null, ...$sources)
    {
        $factory = new Bans\Factory($lang);
        foreach ($sources as $source) {
            $this->sources[] = $factory->whichType($source);
        }
    }

    /**
     * @param string|array<string>|array<int, string>|Sources\ASources ...$toCompare
     * @throws BanException
     * @return bool
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
