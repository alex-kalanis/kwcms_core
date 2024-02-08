<?php

namespace KWCMS\modules\Krep\Libs\Discus;


use kalanis\kw_forums\Content\PostList;
use kalanis\kw_forums\Interfaces\ITargets;
use KWCMS\modules\Krep\Libs;


class RenderSinglePost implements ITargets, Libs\Interfaces\IContent
{
    /** @var Libs\Config */
    protected $config = null;

    public function __construct(Libs\Config $config)
    {
        $this->config = $config;
    }

    public function getContent(Libs\Shared\PageData $pageData, int $postId = 0): string
    {
        $topicTemplate = new Libs\Template('discus_read');
        $foundPost = null;
        foreach ($pageData->getPosts() as $post) {
            if ($post->getId() == $postId) {
                $foundPost = $post;
                break;
            }
        }
        $topicTemplate->reset();
        if (empty($foundPost)) {
            $topicTemplate->change('{POSTID}', '#');
            $topicTemplate->change('{USERNAME}', 'Anonymous');
            $topicTemplate->change('{DATETIME}', 'not found');
            $topicTemplate->change('{CONTENT}', __('not_found'));
        } else {
            $topicTemplate->change('{POSTID}', $foundPost->getId());
            $topicTemplate->change('{USERNAME}', $foundPost->getName());
            $topicTemplate->change('{DATETIME}', $this->postDate($foundPost->getTime()));
            $topicTemplate->change('{CONTENT}', $this->postProcess($foundPost));
        }
        $topicTemplate->change('{TOPIC_NO}', __('topic_no'));
        $topicTemplate->change('{FROM_USER}', __('from_user'));
        $topicTemplate->change('{BACK}', __('back'));
        $topicTemplate->change('{LINK}', $this->topicLink($pageData));
        $topicTemplate->change('{BACK}', __('back'));
        return $topicTemplate->render();
    }

    protected function postDate($time): string
    {
        return date('d.n.Y G:i:s ', $time);
    }

    protected function postProcess(PostList $post): string
    {
        $postContent = $post->getText();
        $postContent = str_replace('/ukazobrazek', 'https://www.k-report.net/ukazobrazek', $postContent);
        $postContent = str_replace('http://www.k-report.net/discus/mes', '/discus.php?addr=www.k-report.net/discus/mes', $postContent);
        $postContent = str_replace('https://www.k-report.net/discus/mes', '/discus.php?addr=www.k-report.net/discus/mes', $postContent);
        $postContent = str_replace('http://www.k-report.net/presmerovani/?pri', '/discus.php?addr=www.k-report.net/presmerovani/%3Fpri', $postContent);
        $postContent = str_replace('https://www.k-report.net/presmerovani/?pri', '/discus.php?addr=www.k-report.net/presmerovani/%3Fpri', $postContent);
        return $postContent;
    }

    protected function topicLink(Libs\Shared\PageData $pageData): string
    {
        return '/discus.php?addr=' . urlencode($this->config->remote_domain . '/discus/messages/' . $pageData->getDiscusNumber() . '/' . $pageData->getTopicNumber() . '.html');
    }
}
