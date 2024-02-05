<?php

namespace KWCMS\modules\Socials\Templates;


use KWCMS\modules\Core\Libs\ATemplate;


/**
 * Class ATmplAccount
 * @package KWCMS\modules\Socials\Templates
 */
class YtVideo extends ATemplate
{
    protected $moduleName = 'Socials';
    protected $templateName = 'yt_video';

    protected function fillInputs(): void
    {
        $this->addInput('{LINK}');
        $this->addInput('{WIDTH}', 560);
        $this->addInput('{HEIGHT}', 315);
    }

    public function setData(string $link, ?int $width = null, ?int $height = null): self
    {
        $this->updateItem('{LINK}', $link);
        if (!is_null($width)) {
            $this->updateItem('{WIDTH}', $width);
        }
        if (!is_null($height)) {
            $this->updateItem('{HEIGHT}', $height);
        }
        return $this;
    }
}
