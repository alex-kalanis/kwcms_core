<?php

namespace KWCMS\modules\Watermark\Libs;


use kalanis\kw_images\Graphics\Processor;
use kalanis\kw_images\ImagesException;


/**
 * Class ImageProcessor
 * @package KWCMS\modules\Watermark\Libs
 * Watermark over images - processor with render
 */
class ImageProcessor extends Processor
{
    /**
     * @param string $type
     * @throws ImagesException
     */
    public function render(string $type): void
    {
        $processor = $this->factory->getByType($type, $this->getImLang());
        $processor->save(null, $this->getResource());
    }
}
