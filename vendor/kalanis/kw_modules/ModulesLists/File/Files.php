<?php

namespace kalanis\kw_modules\ModulesLists\File;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\IProcessFiles;
use kalanis\kw_modules\Interfaces\IMdTranslations;
use kalanis\kw_modules\Interfaces\Lists\IFile;
use kalanis\kw_modules\ModuleException;
use kalanis\kw_modules\Traits\TMdLang;
use kalanis\kw_paths\Interfaces\IPaths;
use kalanis\kw_paths\PathsException;


/**
 * Class Files
 * @package kalanis\kw_modules\ModulesLists\File
 */
class Files implements IFile
{
    use TMdLang;

    /** @var IProcessFiles */
    protected $files = null;
    /** @var string[] */
    protected $moduleConfPath = [];
    /** @var string[] */
    protected $path = [];

    /**
     * @param IProcessFiles $files
     * @param string[] $moduleConfPath
     * @param IMdTranslations|null $lang
     */
    public function __construct(IProcessFiles $files, array $moduleConfPath, ?IMdTranslations $lang = null)
    {
        $this->setMdLang($lang);
        $this->moduleConfPath = $moduleConfPath;
        $this->files = $files;
    }

    public function setModuleLevel(int $level): void
    {
        $this->path = array_merge($this->moduleConfPath, [
            sprintf('%s.%d.%s', IPaths::DIR_MODULE, $level, IPaths::DIR_CONF )
        ]);
    }

    public function load(): string
    {
        try {
            $data = $this->files->readFile($this->getPath());
            return is_resource($data) ? strval(stream_get_contents($data, -1, 0)) : strval($data);
        } catch (FilesException | PathsException $ex) {
            throw new ModuleException($this->getMdLang()->mdStorageLoadProblem(), 0, $ex);
        }
    }

    public function save(string $records): bool
    {
        try {
            return $this->files->saveFile($this->getPath(), $records);
        } catch (FilesException | PathsException $ex) {
            throw new ModuleException($this->getMdLang()->mdStorageSaveProblem(), 0, $ex);
        }
    }

    /**
     * @throws ModuleException
     * @return string[]
     */
    protected function getPath(): array
    {
        if (empty($this->path)) {
            throw new ModuleException($this->getMdLang()->mdConfPathNotSet());
        }
        return $this->path;
    }
}
