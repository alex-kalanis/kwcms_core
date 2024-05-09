<?php

namespace kalanis\kw_files\Processing\Volume;


use kalanis\kw_files\Interfaces\IFLTranslations;
use kalanis\kw_files\Interfaces\IProcessNodes;
use kalanis\kw_files\Processing\TPath;
use kalanis\kw_files\Traits\TLang;
use kalanis\kw_paths\Extras\TPathTransform;
use kalanis\kw_paths\PathsException;


/**
 * Class ProcessNode
 * @package kalanis\kw_files\Processing\Volume
 * Process nodes in basic ways
 */
class ProcessNode implements IProcessNodes
{
    use TLang;
    use TPath;
    use TPathTransform;

    public function __construct(string $path = '', ?IFLTranslations $lang = null)
    {
        $this->setPath($path);
        $this->setFlLang($lang);
    }

    public function exists(array $entry): bool
    {
        return @file_exists($this->fullPath($entry));
    }

    public function isReadable(array $entry): bool
    {
        return @is_readable($this->fullPath($entry));
    }

    public function isWritable(array $entry): bool
    {
        return @is_writable($this->fullPath($entry));
    }

    public function isDir(array $entry): bool
    {
        return @is_dir($this->fullPath($entry));
    }

    public function isFile(array $entry): bool
    {
        return @is_file($this->fullPath($entry));
    }

    public function size(array $entry): ?int
    {
        $path = $this->fullPath($entry);
        $size = @filesize($path);
        return (false === $size) ? null : $size;
    }

    public function created(array $entry): ?int
    {
        $path = $this->fullPath($entry);
        $created = @filemtime($path);
        return (false === $created) ? null : $created;
    }

    /**
     * @param array<string> $path
     * @throws PathsException
     * @return string
     */
    protected function fullPath(array $path): string
    {
        return $this->getPath() . DIRECTORY_SEPARATOR . $this->compactName($path);
    }

    /**
     * @return string
     * @codeCoverageIgnore only when path fails
     */
    protected function noDirectoryDelimiterSet(): string
    {
        return $this->getFlLang()->flNoDirectoryDelimiterSet();
    }
}
