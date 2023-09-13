<?php

namespace KWCMS\modules\Core\Libs;


use kalanis\kw_confs\Config;
use kalanis\kw_paths\Stuff;
use kalanis\kw_routed_paths\Linking;
use kalanis\kw_routed_paths\RoutedPath;


/**
 * Class ExternalLink
 * @package KWCMS\modules\Core\Libs
 * Make links to destinations
 * # [http://www.example.com]/[vardir/][modestate[/[?path=]]][user/][lang/][path/file.htm]
 * # mode - which variant of representing path you call - depends on server settings and KWCMS settings
 * # user - which user is loaded - partially depends on KWCMS settings
 * # lang - which lang is loaded - partially depends on KWCMS settings
 * # path - final path to file
 */
class ExternalLink
{
    protected $useRewrite = false;
    protected $moreLangs = false;
    protected $usedLang = '';

    public function __construct(RoutedPath $routedPath, ?bool $moreUsers = null, ?bool $moreLangs = null)
    {
        $moreUsers = is_null($moreUsers) ? boolval(Config::get('Core', 'site.more_users', false)) : $moreUsers;
        $this->moreLangs = is_null($moreLangs) ? boolval(Config::get('Core', 'page.more_lang', false)) : $moreLangs;
        $this->usedLang = $routedPath->getLang();
        $this->useRewrite = boolval(intval(Config::get('Core', 'site.use_rewrite', false)));

        $this->lib = new Linking\External(
            new Linking\Link(
                $this->useRewrite
                    ? strval(Config::get('Core', 'site.fake_dir', static::PT5))
                    : self::PT1
            ),
            $routedPath->getPath(),
            $moreUsers ? $routedPath->getUser() : ''
        );
    }

    # path setter modifications
    const PT1 = '?path='; # no more than path
    const PT2 = '&path='; # path is in the middle
    const PT3 = 'index.php?path='; # path goes first
    const PT4 = 'index.php/'; # rewrite by php
    const PT5 = '/web/'; # rewrite rule by apache // 'web/' - $settings['site']['fake_dir']

    /**
     * @param string[]|string $path wanted path to target, if null, more lang add current lang as part of path
     * @param string[]|string $module called module name
     * @param bool $single call that as only content
     * @param string|null $lang use that lang
     * @return string
     */
    public function linkVariant($path = null, $module = [], bool $single = false, string $lang = null): string
    {
        $renderPath = is_null($path) ? [] : (is_array($path) ? array_values($path) : Stuff::linkToArray(strval($path)));
        $renderLang = ($this->moreLangs && (is_null($path) || !is_null($lang))) ? ($lang ?: $this->usedLang) : null;
        $renderModule = is_array($module) ? array_values($module) : Stuff::linkToArray(strval($module));
        if ($renderLang) {
            array_unshift($renderPath, $renderLang);
        }
        return ($this->useRewrite ? '/' : '') . $this->lib->link($renderPath, $renderModule, $single);
    }
}
