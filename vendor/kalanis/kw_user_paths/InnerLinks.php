<?php

namespace kalanis\kw_user_paths;


use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\Interfaces\IPaths;
use kalanis\kw_paths\PathsException;
use kalanis\kw_routed_paths\RoutedPath;


/**
 * Class InnerLinks
 * Extend known path to user file with user's props
 */
class InnerLinks
{
    /** @var ArrayPath */
    protected $arrPath = null;
    /** @var RoutedPath */
    protected $routedPath = null;
    /** @var bool */
    protected $moreUsers = false;
    /** @var bool */
    protected $moreLangs = false;
    /** @var string[] */
    protected $prefixPath = [];
    /** @var bool */
    protected $useSystemPrefix = true;
    /** @var bool */
    protected $useDataSeparator = true;

    /**
     * @param RoutedPath $routedPath
     * @param bool $moreUsers
     * @param bool $moreLangs
     * @param string[] $prefixPath path to all user data, depends on storage
     * @param bool $useSystemPrefix add system dir as prefix
     * @param bool $useDataSeparator add system dir as separator of part to user's data itself
     */
    public function __construct(
        RoutedPath $routedPath,
        bool $moreUsers = false,
        bool $moreLangs = false,
        array $prefixPath = [],
        bool $useSystemPrefix = true,
        bool $useDataSeparator = true
    ) {
        $this->arrPath = new ArrayPath();
        $this->routedPath = $routedPath;
        $this->moreUsers = $moreUsers;
        $this->moreLangs = $moreLangs;
        $this->prefixPath = $prefixPath;
        $this->useSystemPrefix = $useSystemPrefix;
        $this->useDataSeparator = $useDataSeparator;
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
            $this->useSystemPrefix ? $this->addModuleSeparator() : [],
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
            $this->useSystemPrefix ? $this->addPrefixSeparator() : [],
            $this->moreUsers ? $this->addUser($this->routedPath->getUser()) : [],
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
            $this->useSystemPrefix ? $this->addPrefixSeparator() : [],
            $this->moreUsers ? $this->addUser($this->routedPath->getUser()) : [],
            $this->useDataSeparator ? $this->addModuleSeparator() : [],
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
            $this->useSystemPrefix ? $this->addPrefixSeparator() : [],
            $this->moreUsers ? $this->addUser($this->routedPath->getUser()) : [],
            $this->useDataSeparator ? $this->addDataSeparator() : [],
            $this->moreLangs ? $this->addLang($this->routedPath->getLang()) : [],
            $current
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
     * @param string $user
     * @throws PathsException
     * @return string[]
     */
    protected function addUser(string $user): array
    {
        return $this->arrPath->setString($user)->getArray();
    }

    /**
     * @return string[]
     */
    protected function addDataSeparator(): array
    {
        return [IPaths::DIR_DATA];
    }

    /**
     * @param string $lang
     * @throws PathsException
     * @return string[]
     */
    protected function addLang(string $lang): array
    {
        return $this->arrPath->setString($lang)->getArray();
    }
}
