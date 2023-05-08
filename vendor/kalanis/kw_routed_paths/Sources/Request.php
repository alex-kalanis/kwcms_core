<?php

namespace kalanis\kw_routed_paths\Sources;


use kalanis\kw_paths\Interfaces\IPaths;


/**
 * Class Request
 * @package kalanis\kw_routed_paths\Sources
 * Input source is Request Uri in preset variables
 * This one is for accessing content with url rewrite engines
 */
class Request extends ASource
{
    /** @var string */
    protected $requestUri = '';
    /** @var string|null */
    protected $virtualDir = '';

    /**
     * @param string $requestUri
     * @param string|null $virtualDir
     *
     * virtual dir has following variants:
     * empty string - cut on start of query string - cut on first possible position
     * null - cut on end of query string - don't know when cut
     * something - cut where you find that "prefix"
     */
    public function __construct(string $requestUri, ?string $virtualDir = null)
    {
        $this->requestUri = $requestUri;
        $this->virtualDir = $virtualDir;
    }

    /**
     * @return array<string|int, mixed|null> $params
     * path is composed from:
     * static part - for subdirectory on server
     * virtual prefix - something to split the string
     * path and other keys - after splitting to describe what client want
     */
    public function getData(): array
    {
        list($path, $params) = $this->explodeInput($this->requestUri);
        list($staticPath, $virtualPrefix, $virtualParamPath) = $this->pathToSegments(urldecode($path), $this->virtualDir);
        return array_merge(
                compact('staticPath', 'virtualPrefix'),
                $this->updateVirtualKeys($this->parseVirtualPath($virtualParamPath)),
                $this->parseParamsToArray($params)
        );
    }

    /**
     * @param string $uri
     * @return string[]
     */
    protected function explodeInput(string $uri): array
    {
        return (false !== strpos($uri, IPaths::SPLITTER_QUOTE)) ? explode(IPaths::SPLITTER_QUOTE, $uri, 2) : [$uri, ''];
    }

    /**
     * @param string $path
     * @param string|null $fakeDir
     * @return array<string|null>
     */
    protected function pathToSegments(string $path, ?string $fakeDir): array
    {
        if (!empty($fakeDir)) {
            $splitPos = mb_strpos($path, $fakeDir);
            if (false !== $splitPos) {
                // got rewrite!
                $statical = mb_substr($path, 0, $splitPos);
                $mask = $fakeDir;
                $virtual = mb_substr($path, $splitPos + mb_strlen($fakeDir));
            } else {
                // no rewrite!
                $statical = $path;
                $mask = null;
                $virtual = null;
            }
        } else {
            if (is_null($fakeDir)) {
                // no rewrite!
                $statical = $path;
                $mask = null;
                $virtual = null;
            } else {
                // no rewrite!
                $statical = null;
                $mask = null;
                $virtual = $path;
            }
        }

        return [$statical, $mask, $virtual];
    }

    /**
     * @param string $param
     * @return array<string|int, array<mixed>|string|int|bool>
     */
    protected function parseParamsToArray(string $param): array
    {
        parse_str(html_entity_decode($param, ENT_QUOTES | ENT_HTML5, 'UTF-8'), $result);
        return $result;
    }

    /**
     * @param string|null $path
     * @return array<string|int, mixed>
     */
    protected function parseVirtualPath(?string $path): array
    {
        $params = [];
        if (is_null($path)) {
            return $params;
        }
        if (false != preg_match_all('#([a-zA-Z0-9_-]+):([a-zA-Z0-9_-]*)#ui', $path, $matches)) {
            // for each containing colons
            foreach ($matches[0] as $index => $match) {
                $params[$matches[1][$index]] = $matches[2][$index];
            }
            // lookup for last part which does not contain colon
            if (false != preg_match_all('#:[a-zA-Z0-9_-]*\/([^:]+)#ui', $path, $found)) {
                $foundPath = end($found[1]);
                if (false !== $foundPath) {
                    $params['path'] = $foundPath;
                }
            }
        } else {
            // no colon, just path
            $params['path'] = $path;
        }
        return $params;
    }

    /**
     * @param array<string|int, mixed> $params
     * @return array<string|int, mixed>
    // U:user
    // M:module
    // MS:solo_module
    // L:lang
     */
    protected function updateVirtualKeys(array $params): array
    {
        $result = [];
        foreach ($params as $key => $param) {
            switch (strtolower(strval($key))) {
                case 'ms':
                    $result['module'] = $param;
                    $result['single'] = true;
                    break;
                case 'm':
                    $result['module'] = $param;
                    break;
                case 'u':
                    $result['user'] = $param;
                    break;
                case 'l':
                    $result['lang'] = $param;
                    break;
                default:
                    $result[$key] = $param;
            }
        }
        return $result;
    }
}
