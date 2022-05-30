<?php

namespace kalanis\kw_modules\Linking;


use kalanis\kw_confs\Config;
use kalanis\kw_paths\Interfaces\IPaths;
use kalanis\kw_paths\Path;


/**
 * Class InternalLink
 * @package kalanis\kw_modules\Linking
 * Make links to local sources
 */
class InternalLink
{
    protected $path = null;
    protected $userPath = '';
    protected $langPath = '';
    protected $moreUsers = false;
    protected $moreLangs = false;

    public function __construct(Path $path, ?bool $moreUsers = null, ?bool $moreLangs = null)
    {
        $this->path = $path;
        $this->moreUsers = is_null($moreUsers) ? boolval(Config::get('Core', 'site.more_users', false)) : $moreUsers ;
        $this->moreLangs = is_null($moreLangs) ? boolval(Config::get('Core', 'page.more_lang', false)) : $moreLangs ;
        $this->setUser($path->getUser());
        $this->setLang($path->getLang());
    }

    public function setUser(string $userPath): self
    {
        $user = IPaths::DIR_USER;
        if ($this->moreUsers) {
            $data = $user . DIRECTORY_SEPARATOR . $userPath;
            if (realpath($this->path->getDocumentRoot() . $this->path->getPathToSystemRoot() . $data . DIRECTORY_SEPARATOR . IPaths::DIR_DATA)) {
                $this->userPath = $data . DIRECTORY_SEPARATOR . IPaths::DIR_DATA;
            } else {
                $this->userPath = $data;
            }
        } else {
            $this->userPath = $user;
        }
        return $this;
    }

    public function setLang(string $lang): self
    {
        if ($this->moreLangs) {
            if (realpath($this->path->getDocumentRoot() . $this->path->getPathToSystemRoot() . $this->userPath . DIRECTORY_SEPARATOR . $lang)) {
                $this->langPath = $lang;
            } else {
                $this->langPath = '';
            }
        } else {
            $this->langPath = '';
        }
        return $this;
    }

    /**
     * Output real path to file on local device
     * @param string|null $path
     * @param bool $withLang
     * @param bool $checkPath
     * @return string|null
     */
    public function userContent(?string $path = null, bool $withLang = false, bool $checkPath = true): ?string
    {
        $lang = $withLang ? $this->langPath : '' ;
        $path = is_null($path) ? $this->path->getPath() : $path ;
        $target = implode(DIRECTORY_SEPARATOR, array_filter(
            [$this->path->getDocumentRoot() . $this->path->getPathToSystemRoot(), $this->userPath, $lang, $path]
        ));
        return $checkPath ? ( realpath($target) ? $target : null ) : $target ;
    }

    public function shortContent(?string $path = null, bool $withLang = false, bool $checkPath = true): ?string
    {
        $lang = $withLang ? $this->langPath : '' ;
        $path = is_null($path) ? $this->path->getPath() : $path ;
        $target = implode(DIRECTORY_SEPARATOR, array_filter(
            [$this->userPath, $lang, $path]
        ));
        return $checkPath ? ( realpath($this->path->getDocumentRoot() . $this->path->getPathToSystemRoot() . DIRECTORY_SEPARATOR . $target) ? $target : null ) : $target ;
    }

    /**
     * Output statical (and real) path to file in system dirs
     * @param string $module
     * @param string $path path to file
     * @return string|null Correct path
     */
    public function moduleContent(string $module, string $path): ?string
    {
        $link = implode(DIRECTORY_SEPARATOR, array_filter(
            [$this->path->getDocumentRoot(), $this->path->getPathToSystemRoot(), IPaths::DIR_MODULE, $module, $path]
        ));
        return realpath($link) ? $link : null;
    }
}
