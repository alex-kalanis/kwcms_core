<?php

namespace kalanis\kw_user_paths;


use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\Interfaces\IPaths;
use kalanis\kw_paths\PathsException;


/**
 * Class UserInnerLinks
 * Extend known path to user file with user's props
 * Limited to work with preset user from system as came from settings
 */
class UserInnerLinks
{
    protected ArrayPath $arrPath;
    protected UserDir $userDir;
    protected ?string $useUser = null;
    /** @var string[] */
    protected array $prefixPath = [];

    /**
     * @param string|null $useUser
     * @param string[] $prefixPath path to all user data, depends on storage
     */
    public function __construct(
        ?string $useUser = null,
        array $prefixPath = []
    ) {
        $this->arrPath = new ArrayPath();
        $this->userDir = new UserDir();
        $this->useUser = $useUser; // config.page.default_user
        $this->prefixPath = $prefixPath; // path where is the storage connected, can be empty
    }

    /**
     * Path to system things
     * @param string $module
     * @param string[] $current
     * @return string[]
     */
    public function toModulePath(string $module, array $current): array
    {
        return array_merge(
            $this->prefixPath,
            $this->addModuleSeparator(),
            [$module],
            $current
        );
    }

    /**
     * Path to user system things
     * @param string[] $current
     * @throws PathsException
     * @return string[]
     */
    public function toUserPath(array $current): array
    {
        return array_merge(
            $this->prefixPath,
            $this->addUser(false),
            $current
        );
    }

    /**
     * Path to user system things
     * @param string $module
     * @param string[] $current
     * @throws PathsException
     * @return string[]
     */
    public function toUserModulePath(string $module, array $current): array
    {
        return array_merge(
            $this->prefixPath,
            $this->addUser(false, true),
            [$module],
            $current
        );
    }

    /**
     * Path to user data
     * @param string[] $current
     * @throws PathsException
     * @return string[]
     */
    public function toFullPath(array $current): array
    {
        return array_merge(
            $this->prefixPath,
            $this->addUser(),
            $current
        );
    }

    /**
     * @param bool $wantDataDir
     * @param bool $wantModuleDir
     * @throws PathsException
     * @return string[]
     */
    protected function addUser(bool $wantDataDir = true, bool $wantModuleDir = false): array
    {
        if (is_null($this->useUser)) {
            return [];
        }
        $this->userDir->setUserPath($this->useUser);

        return array_merge(
            $this->userDir->hasHomeDir() ? $this->addPrefixSeparator() : [],
            $this->arrPath->setString(strval($this->userDir->getUserPath()))->getArray(),
            $this->userDir->hasDataDir() && $wantDataDir ? $this->addDataSeparator() : [],
            $this->userDir->hasDataDir() && $wantModuleDir ? $this->addModuleSeparator() : []
        );
    }

    /**
     * @return string[]
     */
    protected function addModuleSeparator(): array
    {
        return [IPaths::DIR_MODULE];
    }

    /**
     * @return string[]
     */
    protected function addPrefixSeparator(): array
    {
        return [IPaths::DIR_USER];
    }

    /**
     * @return string[]
     */
    protected function addDataSeparator(): array
    {
        return [IPaths::DIR_DATA];
    }
}
