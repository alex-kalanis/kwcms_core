<?php

namespace kalanis\kw_bans\Bans;


class Clearing extends Basic
{
    public function setLookedFor(string $lookedFor): void
    {
        parent::setLookedFor(strtr($lookedFor, ["*" => "", "?" => "", ":" => "", ";" => "", "\\" => "", "/" => ""]));
    }
}
