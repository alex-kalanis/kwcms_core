<?php

namespace kalanis\kw_user_paths;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\IProcessDirs;
use kalanis\kw_files\Interfaces\IProcessNodes;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\Interfaces\IPaths;
use kalanis\kw_paths\PathsException;
use kalanis\kw_user_paths\Traits\TLang;


/**
 * Class Actions
 * low-level work with user dirs
 */
class Actions
{
    use TLang;

    protected UserDir $data;
    protected IProcessNodes $nodes;
    protected IProcessDirs $dirs;

    public function __construct(UserDir $data, IProcessNodes $nodes, IProcessDirs $dirs, ?Interfaces\IUPTranslations $lang = null)
    {
        $this->data = $data;
        $this->nodes = $nodes;
        $this->dirs = $dirs;
        $this->setUpLang($lang);
    }

    /**
     * Create inner path tree
     * @throws PathsException
     * @throws FilesException
     * @return string with inner path
     */
    public function createTree(): string
    {
        $path = $this->data->getFullPath();
        if ($this->isNoPathSet($path)) {
            throw new PathsException($this->getUpLang()->upCannotDetermineUserDir());
        }
        $userPath = $this->data->hasDataDir() ? $path->getArrayDirectory() : $path->getArray();
        if (!$this->dirs->createDir($userPath)) {
            if (!$this->nodes->isDir($userPath)) {
                throw new PathsException($this->getUpLang()->upCannotCreateUserDir());
            }
        }
        if ($this->data->hasDataDir()) {
            $this->dirs->createDir(array_merge($userPath, [IPaths::DIR_DATA]));
            $this->dirs->createDir(array_merge($userPath, [IPaths::DIR_CONF]));
            $this->dirs->createDir(array_merge($userPath, [IPaths::DIR_STYLE]));
        }
        return $path->getString();
    }

    /**
     * Remove data in user's work dir
     * @throws PathsException
     * @throws FilesException
     * @return bool
     */
    public function wipeWorkDir(): bool
    {
        $path = $this->data->getFullPath();
        if ($this->isNoPathSet($path)) {
            throw new PathsException($this->getUpLang()->upCannotDetermineUserDir());
        }
        if ($this->isPathTooShort($path)) { # There surely will be an idiot who want to remove root data
            return false;
        }
        $whichPath = $this->data->hasDataDir()
            ? array_merge($path->getArrayDirectory(), [IPaths::DIR_DATA])
            : $path->getArray();
        return $this->dirs->deleteDir($whichPath, true);
    }

    /**
     * Remove everything in user's special sub dirs
     * @throws PathsException
     * @throws FilesException
     * @return bool
     */
    public function wipeConfDirs(): bool
    {
        $path = $this->data->getFullPath();
        if ($this->isNoPathSet($path)) {
            throw new PathsException($this->getUpLang()->upCannotDetermineUserDir());
        }
        if ($this->isPathTooShort($path)) { # There surely will be an idiot who want to remove root data
            return false;
        }
        if (!$this->data->hasDataDir()) {
            return false;
        }
        $userPath = $path->getArrayDirectory();
        $r1 = $this->dirs->deleteDir(array_merge($userPath, [IPaths::DIR_CONF]), true);
        $r2 = $this->dirs->deleteDir(array_merge($userPath, [IPaths::DIR_STYLE]), true);
        return $r1 && $r2;
    }

    /**
     * Remove everything in user's home dir and that home dir itself
     * @throws PathsException
     * @throws FilesException
     * @return bool
     */
    public function wipeHomeDir(): bool
    {
        $path = $this->data->getFullPath();
        if ($this->isNoPathSet($path)) {
            throw new PathsException($this->getUpLang()->upCannotDetermineUserDir());
        }
        if ($this->isPathTooShort($path)) {
            return false; # urcite se najde i blbec, co bude chtit wipe roota (jeste blbejsi napad, nez jsme doufali) - tudy se odinstalace fakt nedela!
        }
        $userPath = $this->data->hasDataDir() ? $path->getArrayDirectory() : $path->getArray();
        $r1 = $this->dirs->deleteDir($userPath, true);
        $this->data->clear();
        return $r1;
    }

    /**
     * @param ArrayPath $path
     * @throws PathsException
     * @return bool
     */
    protected function isNoPathSet(ArrayPath $path): bool
    {
        return empty($path->getString());
    }

    /**
     * @param ArrayPath $path
     * @throws PathsException
     * @return bool
     */
    protected function isPathTooShort(ArrayPath $path): bool
    {
        return (3 > strlen($path->getString()));
    }
}

/*
//// how it works ////

// create user, its dir, subdirs will be made auto
$u = new UserDir();
$u->setUserName("nom");
$u->process();
$src = 'somewhere/on/volume';
$a = new Actions($u, new \kalanis\kw_files\Processing\Volume\ProcessNode($src), new \kalanis\kw_files\Processing\Volume\ProcessDir($src));
$a->createTree();

// when you do not need subdirs; sometimes necessary
$u->wantDataDir(false);

// user removal
$u = new UserDir();
$u->setUserName("nom"); || $u->setUserPath("dat/dumb/dir");
$u->process();
$src = 'somewhere/on/volume';
$a = new Actions($u, new \kalanis\kw_files\Processing\Volume\ProcessNode($src), new \kalanis\kw_files\Processing\Volume\ProcessDir($src));
$a->wipeWorkDir(); || $a->wipeConfDirs(); || $a->wipeHomeDir(); // by the request and feel

// create subdirs when they aren't
$u = new UserDir();
$u->setUserName("nom"); || $u->setUserPath("dat/dumb/dir");
$u->wantDataDir(true);
$u->process();
$a = new Actions($u, new \kalanis\kw_files\Processing\Volume\ProcessNode($src), new \kalanis\kw_files\Processing\Volume\ProcessDir($src));
$a->createTree();

// then it's good idea to write that updated form into password file - or the user will see strange things
$where = $u->getUserPath();

// current user and its files
$u = new UserDir();
$u->setUserName("nom"); || $u->setUserPath("dat/dumb/dir");
$u->process();
$where = $u->getUserPath();

// did that user have home dir and/or subdirs?
$homedir = $u->hasHomeDir(); // beware, returns bool
$subdirs = $u->hasDataDir(); // beware, returns bool
*/

