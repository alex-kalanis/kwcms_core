<?php

namespace kalanis\kw_connect_inputs\Interfaces;


use Traversable;


/**
 * Interface IEntries
 * @package kalanis\kw_connect_inputs\Interfaces
 * Shared interface fo entries
 */
interface IEntries
{
    /**
     * For which source will data be read
     * @return string
     * @see \kalanis\kw_input\Interfaces\IEntry constants
     */
    public function getSource(): string;

    /**
     * List config entries available for action
     * @return Traversable IEntry
     */
    public function getEntries(): Traversable;
}
