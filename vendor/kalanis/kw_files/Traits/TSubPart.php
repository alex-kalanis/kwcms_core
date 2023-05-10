<?php

namespace kalanis\kw_files\Traits;


/**
 * Trait TSubPart
 * @package kalanis\kw_files\Traits
 * Is path sub-part of another?
 */
trait TSubPart
{
    /**
     * @param string[] $what
     * @param string[] $in
     * @return bool
     */
    protected function isSubPart(array $what, array $in): bool
    {
        $compare = intval(min(count($what), count($in)));
        for ($i = 0; $i<$compare; $i++) {
            if ($what[$i] != $in[$i]) {
                return false;
            }
        }
        return true;
    }
}
