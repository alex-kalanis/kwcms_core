<?php

namespace kalanis\kw_styles\Loaders;


use kalanis\kw_paths\Interfaces\IPaths;
use kalanis\kw_paths\Path;
use kalanis\kw_styles\Interfaces\ILoader;


/**
 * Class PhpLoader
 * @package kalanis\kw_styles\Loaders
 * Load scripts and styles from predefined paths
 */
class PhpLoader implements ILoader
{
    /** @var string[] */
    protected $pathMasks = [
        '%2$s%1$s%3$s%1$s%4$s%1$s%5$s%1$s%6$s%1$s%7$s%1$s%8$s', # user dir, user module with conf name
        '%2$s%1$s%3$s%1$s%4$s%1$s%5$s%1$s%6$s%1$s%7$s%1$s%7$s', # user dir, user module
        '%2$s%1$s%3$s%1$s%4$s%1$s%5$s%1$s%6$s%1$s%7$s', # user dir, all user confs
        '%2$s%1$s%3$s%1$s%4$s%1$s%5$s%1$s%6$s', # user dir, conf named by module
        '%2$s%1$s%5$s%1$s%6$s%1$s%7$s%1$s%8$s', # all modules, main script with name
        '%2$s%1$s%5$s%1$s%6$s%1$s%7$s%1$s%7$s', # all modules, default main script
        '%2$s%1$s%5$s%1$s%6$s%1$s%7$s', # all modules, styles in root
    ];

    /** @var Path */
    protected $pathLib = null;

    public function __construct(Path $path)
    {
        $this->pathLib = $path;
    }

    public function load(string $module, string $wantedPath = ''): string
    {
        $includingPath = $this->contentPath($module, $wantedPath);
        return (!empty($includingPath)) ? $this->includedStyles($includingPath) : '';
    }

    protected function contentPath(string $module, string $conf = ''): ?string
    {
        $basicLookupDir = $this->pathLib->getDocumentRoot() . $this->pathLib->getPathToSystemRoot();
        foreach ($this->pathMasks as $pathMask) {
            $unmasked = sprintf( $pathMask,
                DIRECTORY_SEPARATOR, $basicLookupDir,
                IPaths::DIR_USER, $this->pathLib->getUser(),
                IPaths::DIR_MODULE, $module,
                IPaths::DIR_STYLE, $conf
            );
            $path = realpath($unmasked);
            if ($path && is_file($path)) {
                return $path;
            }
        }
        return null;
    }

    protected function includedStyles(string $path): string
    {
        return (string)@file_get_contents($path);
    }
}
