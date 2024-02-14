<?php

namespace kalanis\kw_langs\Loaders;


use kalanis\kw_langs\Interfaces\ILoader;
use kalanis\kw_paths\Interfaces\IPaths;
use kalanis\kw_paths\Path;
use kalanis\kw_routed_paths\RoutedPath;


/**
 * Class PhpLoader
 * @package kalanis\kw_langs\Loaders
 * Load config data from defined source
 * Contains personalized autoloader for configs!
 */
class PhpLoader implements ILoader
{
    /** @var string[] */
    protected $pathMasks = [
        '%2$s%1$s%5$s%1$s%6$s%1$s%7$s%1$s%8$s%9$s', # all modules, translations in sub dir separated
        '%2$s%1$s%5$s%1$s%6$s%1$s%7$s%1$s%7$s%9$s', # all modules, translations as single file in sub dir
        '%2$s%1$s%5$s%1$s%6$s%1$s%7$s%8$s', # all modules, translations separated
        '%2$s%1$s%5$s%1$s%6$s%1$s%7$s%9$s', # all modules, translations in single file
        '%2$s%1$s%3$s%1$s%4$s%1$s%7$s%1$s%8$s%9$s', # custom user translations separated
        '%2$s%1$s%3$s%1$s%4$s%1$s%7$s%1$s%7$s%9$s', # custom user translation as single file
        '%2$s%1$s%7$s%1$s%8$s%9$s', # in lang root translations separated
        '%2$s%1$s%7$s%1$s%7$s%9$s', # in lang root as single file
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

    public function load(string $module, string $lang): ?array
    {
        $path = $this->contentPath($module, $lang);
        return (!empty($path)) ? $this->includedLang($path) : null;
    }

    /**
     * @param string $module
     * @param string $lang
     * @return string|null
     */
    protected function contentPath(string $module, string $lang): ?string
    {
        $basicLookupDir = $this->pathLib->getDocumentRoot() . $this->pathLib->getPathToSystemRoot();
        foreach ($this->pathMasks as $pathMask) {
            $unmasked = sprintf( $pathMask,
                DIRECTORY_SEPARATOR, $basicLookupDir,
                IPaths::DIR_USER, $this->routedLib->getUser(),
                IPaths::DIR_MODULE, $module,
                IPaths::DIR_LANG, $lang, IPaths::EXT
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
     * @return array<string, string>
     */
    protected function includedLang(string $path): array
    {
        $lang = [];
        include ($path);
        return (array) $lang;
    }
}
