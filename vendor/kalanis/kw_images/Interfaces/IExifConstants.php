<?php

namespace kalanis\kw_images\Interfaces;

/**
 * interface IExifConstants
 * @package kalanis\kw_images\Interfaces
 * @link https://jdhao.github.io/2019/07/31/image_rotation_exif_info/#exif-orientation-flag
 */
interface IExifConstants
{
    public const EXIF_ORIENTATION_NORMAL = 1;
    public const EXIF_ORIENTATION_MIRROR_SIMPLE = 2;
    public const EXIF_ORIENTATION_UPSIDE_DOWN = 3;
    public const EXIF_ORIENTATION_MIRROR_UPSIDE_DOWN = 4;
    public const EXIF_ORIENTATION_MIRROR_ON_LEFT = 5;
    public const EXIF_ORIENTATION_ON_LEFT = 6;
    public const EXIF_ORIENTATION_MIRROR_ON_RIGHT = 7;
    public const EXIF_ORIENTATION_ON_RIGHT = 8;
}
