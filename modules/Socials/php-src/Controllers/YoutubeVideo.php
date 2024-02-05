<?php

namespace KWCMS\modules\Socials\Controllers;


use kalanis\kw_modules\Output;
use KWCMS\modules\Core\Libs\AModule;
use KWCMS\modules\Socials\Templates;


/**
 * Class YoutubeVideo
 * @package KWCMS\modules\Socials\Controllers
 * Social page embed - Youtube Video
 */
class YoutubeVideo extends AModule
{
    /** @var string */
    protected $link = '';
    /** @var int|null */
    protected $width = null;
    /** @var int|null */
    protected $height = null;

    public function __construct(...$constructParams)
    {
        // from global configuration
        $width = isset($constructParams['socials']) && isset($constructParams['socials']['youtube']) && isset($constructParams['socials']['youtube']['width'])
            ? intval($constructParams['socials']['youtube']['width']) : null;
        $height = isset($constructParams['socials']) && isset($constructParams['socials']['youtube']) && isset($constructParams['socials']['youtube']['height'])
            ? intval($constructParams['socials']['youtube']['height']) : null;

        $width = empty($sub) && isset($constructParams['youtube']) && isset($constructParams['youtube']['width'])
            ? intval($constructParams['youtube']['width']) : $width;
        $height = empty($sub) && isset($constructParams['youtube']) && isset($constructParams['youtube']['height'])
            ? intval($constructParams['youtube']['height']) : $height;

        $this->width = $width;
        $this->height = $height;
    }

    public function process(): void
    {
        // from page
        $video = strval($this->getFromParam('youtube', ''));
        $video = empty($video) ? strval($this->getFromParam('video', '')) : $video;
        $video = empty($video) ? strval($this->getFromParam('v', '')) : $video;
        $this->link = empty($video) ? $this->link : $video;

        $width = intval($this->getFromParam('width', 0));
        $width = empty($width) ? intval($this->getFromParam('w', 0)) : $width;
        $this->width = empty($width) ? $this->width : $width;

        $height = intval($this->getFromParam('height', 0));
        $height = empty($height) ? intval($this->getFromParam('h', 0)) : $height;
        $this->height = empty($height) ? $this->height : $height;
    }

    public function output(): Output\AOutput
    {
        $out = new Output\Html();
        $cnt = new Templates\YtVideo();
        return $out->setContent(
            empty($this->link)
                ? ''
                : $cnt->reset()->setData($this->fillLink($this->link), $this->width, $this->height)->render()
        );
    }

    protected function fillLink(string $name): string
    {
        return '//www.youtube.com/embed/' . $name;
    }
}
