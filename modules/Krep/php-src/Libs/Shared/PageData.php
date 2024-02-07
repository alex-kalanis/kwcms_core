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
    /** @var string */
    public $response = '';
    /** @var Content\PostList[] */
    public $posts = [];
    /** @var Content\TopicList[] */
    public $topics = [];
    /** @var int */
    public $showForm = self::FORM_SEND_ALL;
    /** @var int */
    public $listingType = self::LISTING_TOPIC;
    /** @var bool */
    public $canPost = self::IS_ALIVE;
    /** @var string */
    public $encodingRemote = 'utf-8';
    /** @var int */
    public $discusNumber = 0;
    /** @var string */
    public $discusDesc = '';
    /** @var int */
    public $topicNumber = 0;
    /** @var string */
    public $topicDesc = '';
    /** @var int */
    public $levelNumber = 0;
    /** @var string */
    public $levelDesc = '';
    /** @var string */
    public $title = '';
    /** @var string */
    public $announce = '';
    /** @var int */
    public $parentDiscusNumber = 0;
    /** @var int */
    public $currentDiscusNumber = 0;
    /** @var bool */
    public $die = false;
    /** @var int|null */
    public $currentPost = null;

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
