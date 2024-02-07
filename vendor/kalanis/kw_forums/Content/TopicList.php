<?php

namespace kalanis\kw_forums\Content;


/**
 * Class TopicList
 * data about single topic
 *
 * @package kalanis\kw_forums\Content
 */
class TopicList
{
    /** @var int */
    protected $id;
    /** @var int */
    protected $time;
    /** @var int */
    protected $counter;
    /** @var string */
    protected $name;
    /** @var string */
    protected $desc;
    /** @var bool */
    protected $isHeader;

    public function setData(int $id = 0, int $time = 0, string $name = '', string $desc = '', int $counter = 0, bool $isHeader = false): self
    {
        $this->id = $id;
        $this->time = $time;
        $this->name = $name;
        $this->desc = $desc;
        $this->counter = $counter;
        $this->isHeader = $isHeader;
        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTime(): int
    {
        return $this->time;
    }

    public function getCounter(): int
    {
        return $this->counter;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDesc(): string
    {
        return $this->desc;
    }

    public function isHeader(): bool
    {
        return $this->isHeader;
    }
}
