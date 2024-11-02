<?php

namespace KWCMS\modules\Krep\Controllers;


use KWCMS\modules\Krep\Libs;


/**
 * Class Index
 * @package KWCMS\modules\Krep\Controllers
 * Just primary page to get content
 */
class Index extends ADisposition
{
    public function process(): void
    {
    }

    protected function getContent(): string
    {
        $indexInfo = new Libs\Template('index');
        $linksInfo = new Libs\Template('index_links');
        $indexInfo->change('{SMALL_LINK}', $this->config->site_link);
        $indexInfo->change('{WARNING}', __("warning"));
        $indexInfo->change('{HINT}', __("hint"));
        $r = "";
        $domain = $this->config->remote_domain;
        foreach ($this->config->menuLinks as $name => $address) {
            $li = clone $linksInfo;
            $li->change('{ADDR}', 'discus.php?addr=' . urlencode($domain . $address));
            $li->change('{NAME}', $name);
            $li->change('{DOWN}', __("down"));
            $r .= $li->render();
        }
        $indexInfo->change('{LINKS}', $r);
        return $indexInfo->render();
    }
}
