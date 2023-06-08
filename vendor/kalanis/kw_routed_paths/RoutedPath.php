<?php

namespace kalanis\kw_routed_paths;


use kalanis\kw_paths\Interfaces\IPaths;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;


/**
 * Class RoutedPath
 * @package kalanis\kw_routed_paths
 * Parsed route path data
 * Notes:
 * On web:
 * - documentRoot is usually container for value _SERVER['DOCUMENT_ROOT'] - path to the scripts
 * - staticalPath and virtualPrefix are changeable with documentRoot when the content is sent outside
 * - with all these values and constants inside the interface it's possible to make walk through the file tree
 * In admin:
 * - documentRoot is still basic dir from _SERVER
 * - pathToSystemRoot is then transfer from system root to dir where the user dir is stored
 * - user is name of logged user from some source
 * - path is path to currently processed content; depends on module if it's file or dir
 *
 * On Windows following variables contains backslashes as directory separators:
 * - path
 * - documentRoot
 * - pathToSystemRoot
 */
class RoutedPath
{
    /** @var string */
    protected $staticPath = ''; // in browser the path which stay the same and targets the document root from the outside
    /** @var string */
    protected $virtualPrefix = ''; // in browser the separation value between static part and virtual one
    /** @var string */
    protected $user = ''; // user whom content is looked for
    /** @var string */
    protected $lang = ''; // in which language will be content provided, also affects path
    /** @var string[] */
    protected $path = []; // the rest of path
    /** @var string */
    protected $module = ''; // basic module which will be used as default one to present the content
    /** @var bool */
    protected $isSingle = false; // is module the master of page and should be there another as wrapper?

    /**
     * @param Sources\ASource $source
     * @throws PathsException
     */
    public function __construct(Sources\ASource $source)
    {
        $params = $source->getData();
        $this->user = strval($params['user'] ?? $this->user );
        $this->lang = strval($params['lang'] ?? $this->lang );
        $this->path = isset($params['path']) ? Stuff::linkToArray(strval($params['path'])) : $this->path;
        $this->module = strval($params['module'] ?? $this->module );
        $this->isSingle = isset($params['single']);
        $this->staticPath = strval($params['staticPath'] ?? $this->staticPath );
        $this->virtualPrefix = strval($params['virtualPrefix'] ?? $this->virtualPrefix );
    }

    public function getStaticPath(): string
    {
        return $this->staticPath;
    }

    public function getVirtualPrefix(): string
    {
        return $this->virtualPrefix;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function getLang(): string
    {
        return $this->lang;
    }

    /**
     * @return string[]
     */
    public function getPath(): array
    {
        return $this->path;
    }

    public function getModule(): string
    {
        return $this->module;
    }

    public function isSingle(): bool
    {
        return $this->isSingle;
    }

    /**
     * @return array<string, bool|string|int|array<string>>
     */
    public function getArray(): array
    {
        return [
            'user' => $this->user,
            'lang' => $this->lang,
            'path' => implode(IPaths::SPLITTER_SLASH, $this->path),
            'module' => $this->module,
            'isSingle' => $this->isSingle,
            'staticPath' => $this->staticPath,
            'virtualPrefix' => $this->virtualPrefix,
        ];
    }
}
