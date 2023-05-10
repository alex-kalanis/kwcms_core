<?php

namespace kalanis\kw_confs\Loaders;


use kalanis\kw_confs\Interfaces\ILoader;
use kalanis\kw_paths\Interfaces\IPaths;
use kalanis\kw_paths\Path;
use kalanis\kw_routed_paths\RoutedPath;


/**
 * Class PhpLoader
 * @package kalanis\kw_confs
 * Load config data from defined source
 * Contains personalized autoloader for configs!
 */
class PhpLoader implements ILoader
{
    /** @var string[] */
    protected $pathMasks = [
        '%2$s%1$s%3$s%1$s%4$s%1$s%5$s%1$s%6$s%1$s%7$s%1$s%8$s%9$s', # user dir, user module with conf name
        '%2$s%1$s%3$s%1$s%4$s%1$s%5$s%1$s%6$s%1$s%7$s%1$s%7$s%9$s', # user dir, user module
        '%2$s%1$s%3$s%1$s%4$s%1$s%5$s%1$s%6$s%1$s%7$s%9$s', # user dir, all user confs
        '%2$s%1$s%3$s%1$s%4$s%1$s%5$s%1$s%6$s%9$s', # user dir, conf named by module
        '%2$s%1$s%5$s%1$s%6$s%1$s%7$s%1$s%8$s%9$s', # all modules, main conf with name
        '%2$s%1$s%5$s%1$s%6$s%1$s%7$s%1$s%7$s%9$s', # all modules, default main conf
        '%2$s%1$s%5$s%1$s%6$s%1$s%7$s%9$s', # all modules, conf in root
    ];

    /** @var Path */
    protected $pathLib = null;
    /** @var RoutedPath */
    protected $routedLib = null;

    public function __construct(Path $pathLib, RoutedPath $routedLib)
    {
        $this->pathLib = $pathLib;
        $this->routedLib = $routedLib;
    }

    /**
     * @param string $module
     * @param string $conf
     * @return array<string|int, string|int|float|bool|null>
     */
    public function load(string $module, string $conf = ''): ?array
    {
        $path = $this->contentPath($module, $conf);
        return (!empty($path)) ? $this->includedConf($path) : null;
    }

    protected function contentPath(string $module, string $conf = ''): ?string
    {
        $basicLookupDir = $this->pathLib->getDocumentRoot() . $this->pathLib->getPathToSystemRoot();
        foreach ($this->pathMasks as $pathMask) {
            $unmasked = sprintf( $pathMask,
                DIRECTORY_SEPARATOR, $basicLookupDir,
                IPaths::DIR_USER, $this->routedLib->getUser(),
                IPaths::DIR_MODULE, $module,
                IPaths::DIR_CONF, $conf, IPaths::EXT
            );
            $path = realpath($unmasked);
            if ($path && is_file($path)) {
                return $path;
            }

        }
        return null;
    }

    /**
     * @param string $path
     * @return array<string|int, string|int|float|bool|null>
     */
    protected function includedConf(string $path): array
    {
        $config = [];
        include_once ($path);
        return (array) $config;
    }
}
