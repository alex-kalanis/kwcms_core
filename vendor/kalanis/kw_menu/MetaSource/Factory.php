<?php

namespace kalanis\kw_menu\MetaSource;


use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_files\FilesException;
use kalanis\kw_menu\Interfaces\IMetaFileParser;
use kalanis\kw_menu\Interfaces\IMetaSource;
use kalanis\kw_menu\Interfaces\IMNTranslations;
use kalanis\kw_menu\MenuException;
use kalanis\kw_menu\Traits\TLang;
use kalanis\kw_paths\PathsException;
use kalanis\kw_storage\Interfaces\IStorage;


/**
 * Class Factory
 * @package kalanis\kw_menu\MetaSource
 * Metadata source is parsed via class
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
     * @param mixed $parser
     * @throws FilesException
     * @throws MenuException
     * @throws PathsException
     * @return IMetaSource
     */
    public function getSource($params, $parser = null): IMetaSource
    {
        if (is_object($params)) {
            if ($params instanceof IMetaSource) {
                return $params;
            }
            if ($params instanceof CompositeAdapter) {
                return new Files($params, $this->getParser($parser), $this->getMnLang());
            }
            if ($params instanceof IStorage) {
                return new Storage($params, $this->getParser($parser), $this->getMnLang());
            }
        } elseif (is_array($params) && isset($params['source'])) {
            return $this->getSource(
                $params['source'],
                isset($params['parser']) ? $params['parser'] : $parser
            );
        } elseif (is_string($params)) {
            return new Volume($params, $this->getParser($parser));
        }
        throw new MenuException($this->getMnLang()->mnNoAvailableMetaSource());
    }

    /**
     * @param mixed $parser
     * @return IMetaFileParser
     */
    protected function getParser($parser): IMetaFileParser
    {
        return is_object($parser) && ($parser instanceof IMetaFileParser) ? $parser : new FileParser();
    }
}
