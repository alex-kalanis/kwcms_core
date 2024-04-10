<?php

namespace kalanis\kw_files\Access;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces;
use kalanis\kw_files\Processing;
use kalanis\kw_files\Traits\TLang;
use kalanis\kw_paths\PathsException;
use kalanis\kw_storage\Interfaces\IStorage;


/**
 * Class Factory
 * @package kalanis\kw_files\Access
 * Create Composite access to storage
 */
class Factory
{
    use TLang;

    public function __construct(?Interfaces\IFLTranslations $lang = null)
    {
        $this->setFlLang($lang);
    }

    /**
     * @param mixed $param
     * @throws PathsException
     * @throws FilesException
     * @return CompositeAdapter
     */
    public function getClass($param): CompositeAdapter
    {
        if (is_string($param)) {
            return new CompositeAdapter(
                new Processing\Volume\ProcessNode($param, $this->flLang),
                new Processing\Volume\ProcessDir($param, $this->flLang),
                new Processing\Volume\ProcessFile($param, $this->flLang),
                new Processing\Volume\ProcessFile($param, $this->flLang)
            );

        } elseif (is_array($param)) {
            if (isset($param['files'])) {
                return $this->getClass($param['files']);
            }
            if (isset($param['path']) && is_string($param['path'])) {
                return new CompositeAdapter(
                    new Processing\Volume\ProcessNode($param['path'], $this->flLang),
                    new Processing\Volume\ProcessDir($param['path'], $this->flLang),
                    new Processing\Volume\ProcessFile($param['path'], $this->flLang),
                    new Processing\Volume\ProcessFile($param['path'], $this->flLang)
                );

            } elseif (isset($param['source']) && is_string($param['source'])) {
                return new CompositeAdapter(
                    new Processing\Volume\ProcessNode($param['source'], $this->flLang),
                    new Processing\Volume\ProcessDir($param['source'], $this->flLang),
                    new Processing\Volume\ProcessFile($param['source'], $this->flLang),
                    new Processing\Volume\ProcessFile($param['source'], $this->flLang)
                );

            } elseif (isset($param['source']) && is_object($param['source']) && ($param['source'] instanceof IStorage)) {
                return new CompositeAdapter(
                    new Processing\Storage\ProcessNode($param['source'], $this->flLang),
                    new Processing\Storage\ProcessDir($param['source'], $this->flLang),
                    new Processing\Storage\ProcessFile($param['source'], $this->flLang),
                    new Processing\Storage\ProcessFileStream($param['source'], $this->flLang)
                );
            }

        } elseif (is_object($param)) {
            if ($param instanceof CompositeAdapter) {
                return $param;

            } elseif ($param instanceof IStorage) {
                return new CompositeAdapter(
                    new Processing\Storage\ProcessNode($param, $this->flLang),
                    new Processing\Storage\ProcessDir($param, $this->flLang),
                    new Processing\Storage\ProcessFile($param, $this->flLang),
                    new Processing\Storage\ProcessFileStream($param, $this->flLang)
                );
            }
        }

        throw new FilesException($this->getFlLang()->flNoAvailableClasses());
    }
}
