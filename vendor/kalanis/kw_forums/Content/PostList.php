<?php

namespace kalanis\kw_forums\Content;


/**
 * Class PostList
 * data about single post
 *
 * @package kalanis\kw_forums\Content
 */
class PostList
{
    protected int $id = 0;
    protected int $time = 0;
    protected int $counter = 0;
    protected string $name = '';
    protected string $text = '';

    public function setData(int $id = 0, int $time = 0, int $counter = 0, string $name = '', string $text = ''): self
    {
        $this->id = $id;
        $this->time = $time;
        $this->counter = $counter;
        $this->name = $name;
        $this->text = $text;
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

    public function getText(): string
    {
        return $this->text;
    }
}
