<?php

namespace kalanis\kw_menu;


use kalanis\kw_files\FilesException;
use kalanis\kw_menu\Interfaces\IMNTranslations;
use kalanis\kw_menu\Traits\TLang;
use kalanis\kw_paths\PathsException;


/**
 * Class MenuFactory
 * @package kalanis\kw_menu
 * Create menu processing
 */
class MenuFactory
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
     * @return MoreEntries
     */
    public function getMenu($params, $parser = null): MoreEntries
    {
        return new MoreEntries(
            $this->getProcessor($params, $parser),
            (new EntriesSource\Factory($this->getMnLang()))->getSource($params)
        );
    }

    /**
     * @param mixed $params
     * @param mixed $parser
     * @throws FilesException
     * @throws MenuException
     * @throws PathsException
     * @return MetaProcessor
     */
    public function getProcessor($params, $parser = null): MetaProcessor
    {
        return new MetaProcessor(
            (new MetaSource\Factory($this->getMnLang()))->getSource($params, $parser),
            $this->getMnLang()
        );
    }
}
