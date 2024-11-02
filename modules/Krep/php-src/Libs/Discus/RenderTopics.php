<?php

namespace KWCMS\modules\Krep\Libs\Discus;


use kalanis\kw_forums\Content\PostList;
use kalanis\kw_forums\Interfaces\ITargets;
use KWCMS\modules\Krep\Libs;


class RenderTopics implements ITargets, Libs\Interfaces\IContent
{
    public function __construct(
        protected readonly Libs\Config $config,
    )
    {
    }

    public function getContent(Libs\Shared\PageData $pageData): string
    {
        $r = "";
        $topicTemplate = new Libs\Template("discus_topic");
        foreach ($pageData->getPosts() as $post) {
            $topicTemplate->reset();
            $topicTemplate->change('{NUM}', $post->getId());
            $topicTemplate->change('{USERNAME}', $post->getName());
            $topicTemplate->change('{POSTID}', $post->getCounter());
            $topicTemplate->change('{DATETIME}', $this->postDate($post));
            $topicTemplate->change('{CONTENT}', $this->postProcess($post));
            $r .= $topicTemplate->render();
        }
        return $r . $this->renderFormLink($pageData);
    }

    protected function postDate(PostList $postList)
    {
        return date("d.n.Y G:i:s ", $postList->getTime());
    }

    protected function postProcess(PostList $post): string
    {
        $postContent = $post->getText();
        $postContent = str_replace('/ukazobrazek', 'https://www.k-report.net/ukazobrazek', $postContent);
        $postContent = str_replace('http://www.k-report.net/discus/mes', '/discus.php?addr=www.k-report.net/discus/mes', $postContent);
        $postContent = str_replace('https://www.k-report.net/discus/mes', '/discus.php?addr=www.k-report.net/discus/mes', $postContent);
        $postContent = str_replace('http://www.k-report.net/presmerovani/?pri', '/discus.php?addr=www.k-report.net/presmerovani/%3Fpri', $postContent);
        $postContent = str_replace('https://www.k-report.net/presmerovani/?pri', '/discus.php?addr=www.k-report.net/presmerovani/%3Fpri', $postContent);
        $postContent = preg_replace("#::ShowImage:\(([^,]+),([^,]+),([^,]+),([^\)]+)?\)::#is", '<a href="https://www.k-report.net/ukazobrazek.php?soubor=$3.jpg" target="_blank"><img src="https://www.k-report.net/discus/obrazky-male/$1/$2/$3.jpg" style="max-width: 200px; max-height: 130px" title="$4"></a>', $postContent);
        return $postContent;
    }

    protected function renderFormLink(Libs\Shared\PageData $pageData): string
    {
        if (ITargets::FORM_SEND_NOBODY == $pageData->getShowForm()) {
            return '';
        }
        $plus = new Libs\Template("discus_rest");
        $plus->change('{LINK}', $this->linkToAdd($pageData));
        $plus->change('{ADD_ONE}', __("add_post"));
        $plus->change('{ADD_MESS}', __("add_mess"));
        return $plus->render();
    }

    protected function linkToAdd(Libs\Shared\PageData $pageData): string
    {
        return '/add.php?addr=' . urlencode($this->config->remote_domain . '/discus/messages/' . $pageData->getDiscusNumber() . "/" . $pageData->getTopicNumber() . ".html");
    }
}
