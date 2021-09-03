<?php

namespace kalanis\kw_forms\Interfaces;


/**
 * Interface IOriginalValue
 * @package kalanis\kw_forms\Interfaces
 * When control can access original value
 * Also that things where you cannot change used value, just say it's okay ot not
 */
interface IOriginalValue
{
    public function getOriginalValue();
}
