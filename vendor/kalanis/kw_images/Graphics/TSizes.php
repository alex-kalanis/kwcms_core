<?php

namespace kalanis\kw_images\Graphics;


/**
 * Trait TSizes
 * Calculate image sizes
 * @package kalanis\kw_images\Graphics
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
