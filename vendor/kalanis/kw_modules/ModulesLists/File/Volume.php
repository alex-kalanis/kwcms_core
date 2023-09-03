<?php

namespace kalanis\kw_modules\ModulesLists\File;


use kalanis\kw_modules\Interfaces\IMdTranslations;
use kalanis\kw_modules\Interfaces\Lists\IFile;
use kalanis\kw_modules\ModuleException;
use kalanis\kw_modules\Traits\TMdLang;
use kalanis\kw_paths\Interfaces\IPaths;


/**
 * Class Volume
 * @package kalanis\kw_modules\ModulesLists\File
 */
class Volume implements IFile
{
    use TMdLang;

    /** @var string */
    protected $moduleConfPath = '';
    /** @var string */
    protected $path = '';

    public function __construct(string $moduleConfPath, ?IMdTranslations $lang = null)
    {
        $this->setMdLang($lang);
        $this->moduleConfPath = $moduleConfPath;
    }

    public function setModuleLevel(int $level): void
    {
        $this->path = implode(DIRECTORY_SEPARATOR, [
            realpath($this->moduleConfPath),
            sprintf('%s.%d.%s', IPaths::DIR_MODULE, $level, IPaths::DIR_CONF )
        ]);
    }

    public function load(): string
    {
        $data = @ file_get_contents($this->getPath());
        if (false === $data) {
            // @codeCoverageIgnoreStart
            throw new ModuleException($this->getMdLang()->mdStorageLoadProblem());
        }
        // @codeCoverageIgnoreEnd
        return strval($data);
    }

    public function save(string $records): bool
    {
        return boolval(intval(@ file_put_contents($this->getPath(), $records)));
    }

    /**
     * @throws ModuleException
     * @return string
     */
    protected function getPath(): string
    {
        if (empty($this->path)) {
            throw new ModuleException($this->getMdLang()->mdStorageTargetNotSet());
        }
        return $this->path;
    }
}
