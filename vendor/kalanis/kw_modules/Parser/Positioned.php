<?php

namespace kalanis\kw_modules\Parser;


/**
 * Class Positioned
 * @package kalanis\kw_modules\Parser
 * Single record with position data
 */
class Positioned
{
    /** @var string */
    protected $braced = '';
    /** @var string */
    protected $inner = '';
    /** @var int */
    protected $position = 0;

    public function __construct(string $braced, string $inner, int $position = 0)
    {
        $this->braced = $braced;
        $this->inner = $inner;
        $this->position = $position;
    }

    public function getBraced(): string
    {
        return $this->braced;
    }

    public function getInner(): string
    {
        return $this->inner;
    }

    public function getPos(): int
    {
        return $this->position;
    }
}
