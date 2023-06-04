<?php

namespace kalanis\kw_images\Traits;


/**
 * Trait TSizes
 * Calculate image sizes
 * @package kalanis\kw_images\Traits
 */
trait TSizes
{
    /**
     * @param int $currentWidth
     * @param int $maxWidth
     * @param int $currentHeight
     * @param int $maxHeight
     * @return array<string, int>
     */
    protected function calculateSize(int $currentWidth, int $maxWidth, int $currentHeight, int $maxHeight): array
    {
        $newWidth = $currentWidth / $maxWidth;
        $newHeight = $currentHeight / $maxHeight;
        $ratio = max($newWidth, $newHeight); // due this it's necessary to pass all
        $ratio = max($ratio, 1.0);
        $newWidth = (int) ($currentWidth / $ratio);
        $newHeight = (int) ($currentHeight / $ratio);
        return ['width' => $newWidth, 'height' => $newHeight];
    }
}
