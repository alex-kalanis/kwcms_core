<?php

namespace KWCMS\modules\Krep\Libs\Discus;


use kalanis\kw_forums\Content\TopicList;
use kalanis\kw_forums\Interfaces\ITargets;
use KWCMS\modules\Krep\Libs;


class RenderThemas implements ITargets, Libs\Interfaces\IContent
{
    /** @var Libs\Config */
    protected $config = null;
    /** @var Libs\Shared\PageData */
    protected $pageData = null;

    public function __construct(Libs\Config $config)
    {
        $this->config = $config;
        return $this;
    }

    public function getContent(Libs\Shared\PageData $pageData): string
    {
        $r = '';
        $topicTemplate = new Libs\Template('discus_thema');
        $topicTemplateAdd = new Libs\Template('discus_add');
        $topicTemplateSpec = new Libs\Template('discus_thema_spec');
        foreach ($pageData->getTopics() as $topic) {
            if (!empty($topic->getName())) {
                $o = '';
                if ($pageData->canPost()) { // neni archivem nebo zamknuta (pripadne vas do prdele posle k-rep)
                    $topicTemplateAdd->reset();
                    $topicTemplateAdd->change('{ADD}', '/add.php');
                    $topicTemplateAdd->change('{ADD_ONE}', __('add_post'));
                    $o = $topicTemplateAdd->render();
                    unset($p);
                }
                if (!$topic->isHeader()) {
                    $topicTemplate->reset();
                    $topicTemplate->change('{ADD}', $o);
                    $topicTemplate->change('{NAME}', $topic->getName());
                    $topicTemplate->change('{NUM}', $topic->getId());
                    $topicTemplate->change('{LINK}', $this->topicLink($topic, $pageData));
                    $topicTemplate->change('{DATE}', $this->date($topic));
                    $topicTemplate->change('{READ}', __('read'));
                    $topicTemplate->change('{DOWN}', __('down'));
                    $r .= $topicTemplate->render();
                } else {
                    $topicTemplateSpec->reset();
                    $topicTemplateSpec->change('{NAME}', $topic->getName());
                    $r .= $topicTemplateSpec->render();
                }
            }
        }
        return $r;
    }

    protected function topicLink(TopicList $topic, Libs\Shared\PageData $pageData): string
    {
        return '?addr=' . urlencode($this->config->remote_domain . '/discus/messages/' . $pageData->getDiscusNumber() . '/' . $topic->getId() . '.html');
    }

    protected function date(TopicList $topic): string
    {
        return date('d-n-y G:i', $topic->getTime());
    }
}
