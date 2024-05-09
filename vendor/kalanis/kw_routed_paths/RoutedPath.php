<?php

namespace kalanis\kw_routed_paths;


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
    protected string $staticPath = ''; // in browser the path which stay the same and targets the document root from the outside
    protected string $virtualPrefix = ''; // in browser the separation value between static part and virtual one
    protected string $user = ''; // user whom content is looked for
    protected string $lang = ''; // in which language will be content provided, also affects path
    /** @var string[] */
    protected array $path = []; // the rest of path
    /** @var string[] */
    protected array $module = []; // basic module which will be used as default one to present the content
    protected bool $isSingle = false; // is module the master of page and should be there another as wrapper?

    /**
     * @param Sources\ASource $source
     * @throws PathsException
     */
    public function __construct(Sources\ASource $source)
    {
        $params = $source->getData();
        $this->user = strval($params['user'] ?? $this->user );
        $this->lang = strval($params['lang'] ?? $this->lang );
        $this->path = array_filter(isset($params['path']) ? Stuff::linkToArray(strval($params['path'])) : $this->path);
        $this->module = array_filter(isset($params['module']) ? Support::moduleNameFromRequest(strval($params['module'])) : $this->module);
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

    /**
     * @return string[]
     */
    public function getModule(): array
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
            'path' => $this->path,
            'module' => $this->module,
            'isSingle' => $this->isSingle,
            'staticPath' => $this->staticPath,
            'virtualPrefix' => $this->virtualPrefix,
        ];
    }
}
