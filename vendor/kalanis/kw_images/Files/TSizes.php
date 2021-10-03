<?php

namespace kalanis\kw_images\Files;


/**
 * Trait TSizes
 * Calculate image sizes
 * @package kalanis\kw_images\Files
 */
trait TSizes
{
    protected function calculateSize(int $currentWidth, int $maxWidth, int $currentHeight, int $maxHeight): array
    {
        $newWidth = $currentWidth / $maxWidth;
        $newHeight = $currentHeight / $maxHeight;
        $ratio = max($newWidth, $newHeight); // due this it's necessary to pass all
        $ratio = max($ratio, 1.0);
        $newWidth = (int)($currentWidth / $ratio);
        $newHeight = (int)($currentHeight / $ratio);
        return ['width' => $newWidth, 'height' => $newHeight];
    }
}
