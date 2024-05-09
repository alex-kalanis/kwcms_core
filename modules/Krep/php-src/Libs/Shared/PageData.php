<?php

namespace KWCMS\modules\Krep\Libs\Shared;


use kalanis\kw_forums\Content;
use kalanis\kw_forums\Interfaces\ITargets;


/**
 * Class PageData
 * @package KWCMS\modules\Krep\Libs\Shared
 * Parsed page data
 */
class PageData implements ITargets
{
    public string $response = '';
    /** @var Content\PostList[] */
    public array $posts = [];
    /** @var Content\TopicList[] */
    public array $topics = [];
    public int $showForm = self::FORM_SEND_ALL;
    public int $listingType = self::LISTING_TOPIC;
    public bool $canPost = self::IS_ALIVE;
    public string $encodingRemote = 'utf-8';
    public int $discusNumber = 0;
    public string $discusDesc = '';
    public int $topicNumber = 0;
    public string $topicDesc = '';
    public int $levelNumber = 0;
    public string $levelDesc = '';
    public string $title = '';
    public string $announce = '';
    public int $parentDiscusNumber = 0;
    public int $currentDiscusNumber = 0;
    public bool $die = false;
    public ?int $currentPost = null;

    public function getShowForm(): int
    {
        return $this->showForm;
    }

    public function getListingType(): int
    {
        return $this->listingType;
    }

    /**
     * @return Content\PostList[]
     */
    public function getPosts(): array
    {
        return $this->posts;
    }

    /**
     * @return Content\TopicList[]
     */
    public function getTopics(): array
    {
        return $this->topics;
    }

    public function getAnnounce(): string
    {
        return $this->announce;
    }

    public function getCurrentDiscusNumber(): int
    {
        return $this->currentDiscusNumber;
    }

    public function getDiscusDesc(): string
    {
        return $this->discusDesc;
    }

    public function getDiscusNumber(): int
    {
        return $this->discusNumber;
    }

    public function getEncodingRemote(): string
    {
        return $this->encodingRemote;
    }

    public function getLevelDesc(): string
    {
        return $this->levelDesc;
    }

    public function getLevelNumber(): int
    {
        return $this->levelNumber;
    }

    public function getParentDiscusNumber(): int
    {
        return $this->parentDiscusNumber;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getTopicDesc(): string
    {
        return $this->topicDesc;
    }

    public function getTopicNumber(): int
    {
        return $this->topicNumber;
    }

    public function canPost(): bool
    {
        return $this->canPost;
    }
}
