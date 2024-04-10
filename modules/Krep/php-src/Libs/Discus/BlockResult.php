<?php

namespace KWCMS\modules\Krep\Libs\Discus;


use KWCMS\modules\Krep\Libs;


/**
 * Class BlockResult
 * @package KWCMS\modules\Krep\Libs\Discus
 */
class BlockResult
{
    protected Libs\Shared\Links $links;
    protected Libs\Config $config;

    public function __construct(Libs\Shared\Links $links, Libs\Config $config)
    {
        $this->links = $links;
        $this->config = $config;
    }

    public function render(Libs\Interfaces\IContent $content, Libs\Shared\PageData $pageData): string
    {
        $this->config->site_name = $pageData->getTitle();

        $blockTemplate = new Libs\Template('discus');
        $blockTemplate->change('{CONTENT}', $content->getContent($pageData));
        $blockTemplate->change('{NAME}', $pageData->getTitle());
        $blockTemplate->change('{UP}', __("up"));
        $blockTemplate->change('{DOWN}', __("down"));
        $blockTemplate->change('{ARCHIVE}', $this->renderArchive($pageData));
        $blockTemplate->change('{CONTINUE}', $this->links->get($pageData));
        return $blockTemplate->render();
    }

    protected function renderArchive(Libs\Shared\PageData $pageData): string
    {
        if (Libs\Shared\Parser::LISTING_THEMAS == $pageData->getListingType()) {
            return '';
        }
        if (!$pageData->canPost()) {
            return '';
        }
        $archiveTemplate = new Libs\Template("discus_archive");
        $archiveTemplate->change('{ARCHIVE}', __("archive"));
        $archiveTemplate->change('{LINK}', $this->archiveLink($pageData));
        return $archiveTemplate->render();
    }

    protected function archiveLink(Libs\Shared\PageData $pageData): string
    {
        return '?addr=' . urlencode($this->config->remote_domain . '/discus/messages/' . $pageData->getDiscusNumber() . "/" . $pageData->getTopicNumber() . ".html") . "&amp;arch=1";
    }
}
