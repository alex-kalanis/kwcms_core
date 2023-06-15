<?php

namespace kalanis\kw_user_paths;


use InvalidArgumentException;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\Interfaces\IPaths;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;
use kalanis\kw_user_paths\Traits\TLang;
use UnexpectedValueException;


/**
 * Class UserDir
 * Work with user dirs
 */
class UserDir
{
    use TLang;

    /** @var string|null */
    protected $userName = null; # obtained user's name (when need)
    /** @var string|null */
    protected $userPath = null; # system path to user's home dir - as set by values (contains slashes as extra info)
    /** @var string */
    protected $homeDir = ''; # relative path to user's home dir (from storage root)
    /** @var string */
    protected $dataDir = ''; # relative path to user's work dir (from storage root)
    /** @var ArrayPath|null */
    protected $fullPath = null; # full path as derived from user path or user settings
    /** @var bool */
    protected $hasHomeDir = true; # if use sub dirs or is it directly in user's home dir
    /** @var bool */
    protected $hasDataDir = true; # if use user dir or is it anywhere else directly from web root

    public function __construct(?Interfaces\IUPTranslations $lang = null)
    {
        $this->setUpLang($lang);
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
    public function getDataDir(): string
    {
        return $this->dataDir;
    }

    /**
     * Return full path class with current user's dir
     * @throws PathsException
     * @return ArrayPath
     */
    public function getFullPath(): ArrayPath
    {
        if (empty($this->fullPath)) {
            throw new PathsException($this->getUpLang()->upCannotGetFullPaths());
        }
        return $this->fullPath;
    }

    /**
     * Clear data
     * @return $this
     */
    public function clear(): self
    {
        $this->userName = null;
        $this->userPath = null;
        $this->homeDir = '';
        $this->dataDir = '';
        $this->fullPath = null;
        $this->hasHomeDir = true;
        $this->hasDataDir = true;
        return $this;
    }

    /**
     * Set username as base for generating user dir
     * @param string $name
     * @throws InvalidArgumentException
     * @return $this
     */
    public function setUserName(string $name): self
    {
        if (empty($name)) {
            throw new InvalidArgumentException($this->getUpLang()->upUserNameIsShort());
        }
        if (false !== strpbrk($name, '.: /~')) {
            throw new InvalidArgumentException($this->getUpLang()->upUserNameContainsChars());
        }
        $this->userName = $name;
        return $this;
    }

    public function getUserName(): ?string
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
        $this->hasHomeDir = $set;
        return $this;
    }

    public function hasHomeDir(): bool
    {
        return $this->hasHomeDir;
    }

    /**
     * Use sub dirs with data?
     * @param bool $set
     * @return UserDir
     */
    public function wantDataDir(bool $set): self
    {
        $this->hasDataDir = $set;
        return $this;
    }

    public function hasDataDir(): bool
    {
        return $this->hasDataDir;
    }

    /**
     * Set obtained path as basic user dir
     * @param string|null $path
     * @return bool
     */
    public function setUserPath(?string $path): bool
    {
        $this->clear();
        if (is_null($path)) {
            return true;
        }
        if (false !== strpbrk($path, ':')) {
            return false;
        }
        $this->hasHomeDir = IPaths::SPLITTER_SLASH != substr($path, 0, 1); # may use data dir - does not start with slash
        $this->hasDataDir = IPaths::SPLITTER_SLASH != substr($path, -1, 1); # may use sub dirs - does not end with slash
        $this->userPath = $path;
        return true;
    }

    public function getUserPath(): ?string
    {
        return $this->userPath;
    }

    /**
     * Fill user dir from obtained params, must run every time
     * @throws PathsException
     * @return $this
     */
    public function process(): self
    {
        if (empty($this->userPath)) {
            $this->userPath = $this->makeFromUserName();
        }

        $this->homeDir = $this->hasHomeDir
            ? IPaths::DIR_USER
            : '.';
        $this->dataDir = $this->hasDataDir
            ? IPaths::DIR_DATA
            : '.';

        $this->fullPath = new ArrayPath();
        $this->fullPath->setArray(array_merge(
            [$this->homeDir],
            Stuff::pathToArray($this->userPath, IPaths::SPLITTER_SLASH),
            [$this->dataDir]
        ));
        return $this;
    }

    protected function makeFromUserName(): string
    {
        if (empty($this->userName)) {
            throw new UnexpectedValueException($this->getUpLang()->upUserNameNotDefined());
        }
        $userPath = $this->userName;
        if (!$this->hasHomeDir) {
            $userPath = IPaths::SPLITTER_SLASH . $userPath;
        }
        if (!$this->hasDataDir) {
            $userPath = $userPath . IPaths::SPLITTER_SLASH;
        }
        return $userPath;
    }
}

