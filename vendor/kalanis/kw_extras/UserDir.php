<?php

namespace kalanis\kw_extras;


use InvalidArgumentException;
use kalanis\kw_paths\Interfaces\IPaths;
use kalanis\kw_paths\Path;
use kalanis\kw_paths\Stuff;
use UnexpectedValueException;


/**
 * Class UserDir
 * low-level work with user dirs
 */
class UserDir
{
    protected $userName = ''; # obtained user's name (when need)
    protected $userPath = ''; # system path to user's home dir
    protected $webRootDir = ''; # system path to web root dir
    protected $workDir = ''; # relative path to user's work dir (from web dir)
    protected $homeDir = ''; # relative path to user's home dir (from web dir)
    protected $realPath = ''; # real path as derived from user path - without added slashes
    protected $canUseHomeDir = true; # if use sub dirs or is it directly in user's home dir
    protected $canUseDataDir = true; # if use user dir or is it anywhere else directly from web root

    public function __construct(Path $path)
    {
        $this->webRootDir =
            Stuff::removeEndingSlash($path->getDocumentRoot()) . DIRECTORY_SEPARATOR
            . Stuff::removeEndingSlash($path->getPathToSystemRoot()) . DIRECTORY_SEPARATOR;
    }

    public function getWebRootDir(): string
    {
        return $this->webRootDir;
    }

    /**
     * Return relative path to home dir for accessing special dirs
     * @return string
     */
    public function getHomeDir(): string
    {
        return $this->homeDir;
    }

    /**
     * Return relative path to working dir
     * @return string
     */
    public function getWorkDir(): string
    {
        return $this->workDir;
    }

    /**
     * Return real path to working dir
     * @return string
     */
    public function getRealDir(): string
    {
        return $this->realPath;
    }

    /**
     * Set username as base for generating user dir
     * @param string $name
     * @return UserDir
     * @throws InvalidArgumentException
     */
    public function setUserName(string $name): self
    {
        if (empty($name)) {
            throw new InvalidArgumentException('USERNAME_IS_SHORT');
        }
        if (false !== strpbrk($name, '.: /~')) {
            throw new InvalidArgumentException('USERNAME_CONTAINS_UNSUPPORTED_CHARS');
        }
        $this->userName = $name;
        return $this;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    /**
     * Use home as data dir?
     * @param bool $set
     * @return UserDir
     */
    public function wantHomeDir(bool $set): self
    {
        $this->canUseHomeDir = $set;
        return $this;
    }

    public function usedHomeDir(): bool
    {
        return $this->canUseHomeDir;
    }

    /**
     * Use sub dirs?
     * @param bool $set
     * @return UserDir
     */
    public function wantDataDir(bool $set): self
    {
        $this->canUseDataDir = $set;
        return $this;
    }

    public function usedDataDir(): bool
    {
        return $this->canUseDataDir;
    }

    /**
     * Set obtained path as basic user dir
     * @param string $path
     * @return bool
     */
    public function setUserPath(string $path): bool
    {
        if (false !== strpbrk($path, ':')) {
            return false;
        }
        $this->canUseHomeDir = DIRECTORY_SEPARATOR != substr($path, 0, 1); # may use data dir - does not start with slash
        $this->canUseDataDir = DIRECTORY_SEPARATOR != substr($path, -1, 1); # may use sub dirs - does not end with slash
        $this->userPath = $path;
        return true;
    }

    public function getUserPath(): string
    {
        return $this->userPath;
    }

    /**
     * Fill user dir from obtained params, must run every time
     * @return $this
     */
    public function process(): self
    {
        if (empty($this->userPath)) {
            $this->userPath = $this->makeFromUserName();
        }

        $this->realPath = Stuff::sanitize($this->userPath);
        $this->homeDir = $this->canUseHomeDir
            ? Stuff::removeEndingSlash(IPaths::DIR_USER . DIRECTORY_SEPARATOR . $this->realPath) . DIRECTORY_SEPARATOR
            : $this->realPath . DIRECTORY_SEPARATOR;
        $this->workDir = $this->canUseDataDir
            ? $this->homeDir . IPaths::DIR_DATA . DIRECTORY_SEPARATOR
            : $this->homeDir;
        return $this;
    }

    protected function makeFromUserName(): string
    {
        if (empty($this->userName)) {
            throw new UnexpectedValueException('NO_USERPATH_PARAMS');
        }
        $userPath = $this->userName;
        if (!$this->canUseHomeDir) {
            $userPath = DIRECTORY_SEPARATOR . $userPath;
        }
        if (!$this->canUseDataDir) {
            $userPath = $userPath . DIRECTORY_SEPARATOR;
        }
        return $userPath;
    }

    /**
     * Create inner path tree
     * @return string with inner path
     * @throws ExtrasException
     */
    public function createTree(): string
    {
        if (empty($this->homeDir)) {
            throw new ExtrasException('CANNOT_DETERMINE_USER_DIR');
        }
        $userDir = Stuff::removeEndingSlash($this->homeDir);
        if (!mkdir($this->webRootDir . $userDir)) {
            if (!is_dir($this->webRootDir . $userDir)) {
                throw new ExtrasException('CANNOT_CREATE_USER_DIR');
            }
        }
        if ($this->canUseDataDir) {
            mkdir($this->webRootDir . $this->homeDir . IPaths::DIR_DATA);
            mkdir($this->webRootDir . $this->homeDir . IPaths::DIR_CONF);
            mkdir($this->webRootDir . $this->homeDir . IPaths::DIR_STYLE);
        }
        return $userDir;
    }

    /**
     * Remove data in user's work dir
     * @return bool
     * @throws ExtrasException
     */
    public function wipeWorkDir(): bool
    {
        if (empty($this->workDir)) {
            throw new ExtrasException('CANNOT_DETERMINE_USER_DIR');
        }
        if (3 > strlen($this->workDir)) {
            return false; # urcite se najde i blbec, co bude chtit cistku roota
        }
        $this->removeCycle($this->webRootDir . $this->workDir);
        return true;
    }

    /**
     * Remove everything in user's special sub dirs
     * @return bool
     * @throws ExtrasException
     */
    public function wipeConfDirs(): bool
    {
        if (empty($this->homeDir)) {
            throw new ExtrasException('CANNOT_DETERMINE_USER_DIR');
        }
        if (!$this->canUseDataDir) {
            return false;
        }
        if (strlen($this->homeDir) < 3) {
            return false; # urcite se najde i blbec, co bude chtit cistku roota
        }
        $this->removeCycle($this->webRootDir . $this->homeDir . IPaths::DIR_CONF);
        $this->removeCycle($this->webRootDir . $this->homeDir . IPaths::DIR_STYLE);
        return true;
    }

    /**
     * Remove everything in user's home dir and that home dir itself
     * @return bool
     * @throws ExtrasException
     */
    public function wipeHomeDir(): bool
    {
        if (!is_string($this->homeDir)) {
            throw new ExtrasException('CANNOT_DETERMINE_USER_DIR');
        }
        if (strlen($this->workDir) < 4) {
            return false; # urcite se najde i blbec, co bude chtit wipe roota (jeste blbejsi napad, nez jsme doufali) - tudy se odinstalace fakt nedela!
        }
        $this->removeCycle($this->webRootDir . $this->homeDir);
        rmdir($this->webRootDir . Stuff::removeEndingSlash($this->homeDir));
        $this->workDir = '';
        $this->homeDir = '';
        return true;
    }

    /**
     * Remove sub dirs and their content recursively
     * SHALL NOT BE SEPARATED INTO EXTRA CLASS
     * @param $dirPath
     */
    protected function removeCycle(string $dirPath): void
    {
        $path = Stuff::removeEndingSlash($dirPath);
        foreach (scandir($path) as $fileName) {
            if (is_dir($path . DIRECTORY_SEPARATOR . $fileName)) {
                if (($fileName != '.') || ($fileName != '..')) {
                    $this->removeCycle($path . DIRECTORY_SEPARATOR . $fileName);
                    rmdir($path . DIRECTORY_SEPARATOR . $fileName);
                }
            } else {
                unlink($path . DIRECTORY_SEPARATOR . $fileName);
            }
        }
    }
}

/*
postupy zpracovani

vyroba uzivatele a jeho slozky, podslozky to vyrabi automaticky
$u = new UserDir();
$u->setUserName("nom");
$u->process();
$u->createTree();

zakaz vyrabet podslozky, obcas potreba
$u->wantDataDir(false);

likvidace uzivatele
$u = new UserDir();
$u->setUserName("nom"); || $u->setUserPath("dat/dumb/dir");
$u->process();
$u->wipeWorkDir(); || $u->wipeConfDirs(); || $u->wipeHomeDir(); // dle pozadavku a nalady

vyroba podslozek, pokud nejsou
$u = new UserDir();
$u->setUserName("nom"); || $u->setUserPath("dat/dumb/dir");
$u->wantDataDir(true);
$u->process();
$u->createTree();
potom je jeste potreba zapsat zmenu (upravenou cestu) do passwd, jinak uzivatel uvidi blbosti
$kde = $u->getUserPath();

aktualni uzivatel a kde se flakaji jeho soubory
$u = new UserDir();
$u->setUserName("nom"); || $u->setUserPath("dat/dumb/dir");
$u->process();
$kam = $u->getUserPath(); // paradoxne to takto je, nejdriv mu touhle funkci cestu nadirigujete a stejna fce vam vyklopi uz cestu spravnou

jak blbe na tom jsem s userroot (a.k.a. home/ nebo user/) a podslozkami?
$homedir = $u->usedHomeDir(); // bacha, prileti boolean
$subdirs = $u->usedDataDir(); // bacha, prileti boolean
*/

