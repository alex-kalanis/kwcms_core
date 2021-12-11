<?php

namespace kalanis\kw_lookup\Configs;


use kalanis\kw_lookup\Interfaces;
use kalanis\kw_input\Interfaces\IEntry as Input;
use Traversable;


/**
 * Class AEntries
 * @package kalanis\kw_lookup\Configs
 * Simple entry of configuration
 */
abstract class AEntries implements Interfaces\IEntries
{
    protected $source = '';
    /** @var Interfaces\IEntry[] */
    protected $entries = [];

    protected static $availableSources = [
        Input::SOURCE_CLI,
        Input::SOURCE_GET,
        Input::SOURCE_POST,
        Input::SOURCE_SESSION,
    ];

    public function getSource(): string
    {
        return $this->source;
    }

    public function setSource(string $source): self
    {
        $this->source = $this->limitSource($source);
        return $this;
    }

    protected function limitSource(string $source): string
    {
        return in_array($source, static::$availableSources) ? $source : $this->source;
    }

    public function getEntries(): Traversable
    {
        yield from $this->entries;
    }

    public function clear(): self
    {
        $this->entries = [];
        return $this;
    }
}
