<?php

namespace kalanis\kw_paths;


/**
 * Class Path
 * @package kalanis\kw_paths
 * Parsed path data
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
class Path
{
    /** @var string */
    protected $documentRoot = ''; // document root as set from server
    /** @var string */
    protected $pathToSystemRoot = ''; // because document root could not be every time that dir in which are user data dir

    /**
     * @param string $documentRoot
     * @throws PathsException
     * @return $this
     */
    public function setDocumentRoot(string $documentRoot): self
    {
        $this->documentRoot = Stuff::arrayToPath(Stuff::linkToArray($documentRoot));
        return $this;
    }

    public function getDocumentRoot(): string
    {
        return $this->documentRoot;
    }

    /**
     * @param string $pathToSystemRoot
     * @throws PathsException
     * @return $this
     */
    public function setPathToSystemRoot(string $pathToSystemRoot): self
    {
        $this->pathToSystemRoot = Stuff::arrayToPath(Stuff::linkToArray($pathToSystemRoot));
        return $this;
    }

    public function getPathToSystemRoot(): string
    {
        return $this->pathToSystemRoot;
    }
}
