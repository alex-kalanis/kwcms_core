<?php

namespace KWCMS\modules\Krep\Libs\Shared;


use KWCMS\modules\Krep\Libs;


/**
 * Class Links
 * @package KWCMS\modules\Krep\Libs\Shared
 */
class Links
{
    public function __construct(
        protected readonly Libs\Config $config,
    )
    {
    }

    public function get(Libs\Shared\PageData $pageData, bool $alwaysShowThema = false): string
    {
        $continueTemplate = new Libs\Template('continue');

        $isListing = ($pageData->getDiscusNumber() == $pageData->getCurrentDiscusNumber() );
        $isThema = ($pageData->getTopicNumber() == $pageData->getLevelNumber());
        $isArchive = $isThema && !$pageData->canPost();
        $showThema = !($isListing || $isThema) || $isArchive || $alwaysShowThema ;
        $showListing = !$isListing;

        $continueTemplate->change('{LINK_DISCUS}', $showThema ? $this->linkToDiscus($pageData) : '#' );
        $continueTemplate->change('{LINK_THEMES}', $showListing ? $this->linkToThema($pageData) : '#' );
        $continueTemplate->change('{LINK_BEGIN}', '/');
        $continueTemplate->change('{BACK_TO_DISCUS}', $showThema ? __("back_discus_d") : '' );
        $continueTemplate->change('{BACK_TO_THEMES}', $showListing ? __("back_themes") : '' );
        $continueTemplate->change('{BACK_TO_BEGIN}', __("back_begin"));
        return $continueTemplate->render();
    }

    protected function linkToDiscus(Libs\Shared\PageData $pageData): string
    {
        return '/discus.php?addr=' . urlencode($this->config->remote_domain . '/discus/messages/' . $pageData->getDiscusNumber() . "/" . $pageData->getLevelNumber() . ".html") . "#dwn";
    }

    protected function linkToThema(Libs\Shared\PageData $pageData): string
    {
        return '/discus.php?addr=' . urlencode($this->config->remote_domain . '/discus/messages/' . $pageData->getDiscusNumber() . "/" . $pageData->getDiscusNumber() . ".html") . "#d_" . $pageData->getLevelNumber();
    }
}
