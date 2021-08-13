<?php

namespace kalanis\kw_modules\Interfaces;


/**
 * Class ISitePart
 * @package kalanis\kw_modules\Interfaces
 * Which part of site will be rendered
 */
interface ISitePart
{
    const SITE_NOWHERE   = 0; # cannot directly use in any part of site, dummy modules
    const SITE_RESPONSE  = 1; # basic modules accessing the whole response (watermark image, rss, default page content, ...) - GET/POST
    const SITE_LAYOUT    = 2; # modules which will be loaded by other modules as part of layout (themes, logo, menu, langs) - <html>
    const SITE_CONTENT   = 3; # modules which will be loaded by other modules as part of page content (dirlist, galleries, ...) - <body>
    const SITE_ROUTED    = 4; # modules which will be accessed via router

    const KEY_LEVEL = 'level'; # when pass as an argument this one determine which module conf will be loaded
}
