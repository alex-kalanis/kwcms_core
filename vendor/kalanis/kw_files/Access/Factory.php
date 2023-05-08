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
        $this->setLang($lang);
    }

    /**
     * @param string|array<string|int, string|int|float|bool|object>|object $param
     * @throws PathsException
     * @throws FilesException
     * @return CompositeAdapter
     */
    public function getClass($param): CompositeAdapter
    {
        if (is_string($param)) {
            return new CompositeAdapter(
                new Processing\Volume\ProcessNode($param, $this->lang),
                new Processing\Volume\ProcessDir($param, $this->lang),
                new Processing\Volume\ProcessFile($param, $this->lang)
            );

        } elseif (is_array($param)) {
            if (isset($param['path']) && is_string($param['path'])) {
                return new CompositeAdapter(
                    new Processing\Volume\ProcessNode($param['path'], $this->lang),
                    new Processing\Volume\ProcessDir($param['path'], $this->lang),
                    new Processing\Volume\ProcessFile($param['path'], $this->lang)
                );

            } elseif (isset($param['source']) && is_string($param['source'])) {
                return new CompositeAdapter(
                    new Processing\Volume\ProcessNode($param['source'], $this->lang),
                    new Processing\Volume\ProcessDir($param['source'], $this->lang),
                    new Processing\Volume\ProcessFile($param['source'], $this->lang)
                );

            } elseif (isset($param['source']) && is_object($param['source']) && ($param['source'] instanceof IStorage)) {
                return new CompositeAdapter(
                    new Processing\Storage\ProcessNode($param['source'], $this->lang),
                    new Processing\Storage\ProcessDir($param['source'], $this->lang),
                    new Processing\Storage\ProcessFile($param['source'], $this->lang)
                );
            }

        } elseif (is_object($param)) {
            if ($param instanceof IStorage) {
                return new CompositeAdapter(
                    new Processing\Storage\ProcessNode($param, $this->lang),
                    new Processing\Storage\ProcessDir($param, $this->lang),
                    new Processing\Storage\ProcessFile($param, $this->lang)
                );
            }
        }

        throw new FilesException($this->getLang()->flNoAvailableClasses());
    }
}
