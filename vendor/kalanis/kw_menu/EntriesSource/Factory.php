<?php

namespace kalanis\kw_menu\EntriesSource;


use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_files\Access\Factory as composite_factory;
use kalanis\kw_files\FilesException;
use kalanis\kw_menu\Interfaces\IEntriesSource;
use kalanis\kw_menu\Interfaces\IMNTranslations;
use kalanis\kw_menu\MenuException;
use kalanis\kw_menu\Traits\TLang;
use kalanis\kw_paths\PathsException;
use kalanis\kw_storage\Interfaces\IStorage;
use kalanis\kw_tree\Interfaces\ITree;


/**
 * Class Factory
 * @package kalanis\kw_menu\EntriesSource
 * Entries source is in parsed via class
 */
class Factory
{
    use TLang;

    public function __construct(?IMNTranslations $lang = null)
    {
        $this->setMnLang($lang);
    }

    /**
     * @param mixed $params
     * @throws FilesException
     * @throws MenuException
     * @throws PathsException
     * @return IEntriesSource
     */
    public function getSource($params): IEntriesSource
    {
        if (is_object($params)) {
            if ($params instanceof IEntriesSource) {
                return $params;
            }
            if ($params instanceof ITree) {
                return new Tree($params);
            }
            if ($params instanceof CompositeAdapter) {
                return new Files($params);
            }
            if ($params instanceof IStorage) {
                return new Storage($params);
            }
        } elseif (is_array($params) && isset($params['source'])) {
            return $this->getSource($params['source']);
        } elseif (is_string($params)) {
            return new Volume($params);
        }
        throw new MenuException($this->getMnLang()->mnNoAvailableEntrySource());
    }
}
