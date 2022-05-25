<?php

namespace kalanis\kw_modules\Linking;


use kalanis\kw_confs\Config;
use kalanis\kw_paths\Interfaces\IPaths;
use kalanis\kw_paths\Path;
use kalanis\kw_paths\Stuff;


/**
 * Class ExternalLink
 * @package kalanis\kw_modules\Linking
 * Make links to destinations
# [http://www.example.com]/[vardir/][modestate[/[?path=]]][user/][lang/][path/file.htm]
# rdir - dir with physical instalation of KWCMS (root)
# mode - which variant of representing path you call - depends on server settings and KWCMS settings
# user - which user is loaded - partially depends on KWCMS settings
# lang - which lang is loaded - partially depends on KWCMS settings
# path - final path to file
 *
 * @todo: jeste pokracuje refaktor!!!
 * - je tam treba vyhodit nepouzivate veci a preorat jejich navraty na ty pouzivane
 */
class ExternalLink
{
    const MOD_NORMAL = 'm:'; # show in normal mode
    const MOD_SINGLE = 'ms:'; # show module as single window

    protected $path = null;
    protected $moreUsers = false;
    protected $moreLangs = false;

    public function __construct(Path $path, ?bool $moreUsers = null, ?bool $moreLangs = null)
    {
        $this->path = $path;
        $this->moreUsers = is_null($moreUsers) ? boolval(Config::get('Core', 'site.more_users', false)) : $moreUsers ;
        $this->moreLangs = is_null($moreLangs) ? boolval(Config::get('Core', 'page.more_lang', false)) : $moreLangs ;
        $this->connectBy = boolval(Config::get('Core', 'site.use_rewrite', false))
            ? '/' . strval(Config::get('Core', 'site.fake_dir', static::PT5))
            : self::PT1;
    }

    # path setter modifications
    const PT = 'http://';
    const PT1 = '?path='; # no more than path
    const PT2 = '&path='; # path is in the middle
    const PT3 = 'index.php?path='; # path goes first
    const PT4 = 'index.php/'; # rewrite by php
    const PT5 = 'web/'; # rewrite rule by apache // 'web/' - $settings["site"]["fake_dir"]

    private $connectBy = self::PT1;

    /*
        Function: link_variant()
        output dynamical (and system-depend) path to file

        Parametres:
          $path - string, path to file, if null, more lang add current lang as part of path
          $file - string, filename
          $module - string, called module name
          $single - boolean, call that as only content
          $lang - boolean, need system-defined lang

        Returns:
          Correct path
    */
    public function linkVariant(?string $path=null, string $module="", bool $single=false, string $lang=null): string
    {
        $renderUser = $this->moreUsers ? $this->path->getUser() : "" ;
        $renderPath = (is_null($path)) ? $this->path->getPath() : $path ;
        $renderLang = ($this->moreLangs && (is_null($path) || !is_null($lang))) ? ($lang ?: $this->path->getLang()) : '' ;
        $renderModule = "";
        if (strlen($module) > 0) {
            $renderModule = ($single) ? self::MOD_SINGLE : self::MOD_NORMAL ;
            $renderModule .= $module.IPaths::SPLITTER_SLASH;
        }
        return $this->connectBy.$renderModule.$renderUser.$renderLang.$renderPath;
    }

    /*
        Function: link_static()
        output statical (and real) path to file in user dir

        Parametres:
          $path - string, path to file, if null, more lang add current lang as part of path
          $addr - boolean, use address
          $lang - boolean, need system-defined lang

        Returns:
          Correct path
    */
    public function linkStatic(string $path=null, string $addr='', ?string $lang=null): ?string
    {
        $pt = (is_null($path)) ? $this->path->getPath() : $path ;
        $ad = !empty($addr) ? $addr : IPaths::SPLITTER_SLASH ;
        $langs = ($this->moreLangs && (is_null($path) || ($lang))) ? ($lang ?: $this->path->getLang()) : '' ;
        $link = $this->path->getUser().$langs.$pt; // full
//print_r($this);
//print_r(array($pt,$ad,$link));
        return (@file_exists($this->path->getDocumentRoot().$this->path->getPathToSystemRoot().$link))
            ? Stuff::sanitize($ad.$link)
            : null ;
    }

    /**
     * Output statical (and real) path to file in system dirs
     * @param string $module
     * @param string $path path to file
     * @return string|null Correct path
     */
    public function linkModule(string $module, string $path): ?string
    {
        $link = implode(DIRECTORY_SEPARATOR, [$this->path->getPathToSystemRoot(), IPaths::DIR_MODULE, $module, $path]);
        return realpath($this->path->getDocumentRoot() . $link) ? $link : null;
    }
}
