<?php

namespace kalanis\kw_modules\ModulesLists\File;


use kalanis\kw_modules\Interfaces\IMdTranslations;
use kalanis\kw_modules\Interfaces\Lists\IFile;
use kalanis\kw_modules\ModuleException;
use kalanis\kw_modules\Traits\TMdLang;
use kalanis\kw_paths\Interfaces\IPaths;
use kalanis\kw_paths\Stuff;
use kalanis\kw_storage\Interfaces\IStorage as IKwStorage;
use kalanis\kw_storage\StorageException;


/**
 * Class Storage
 * @package kalanis\kw_modules\ModulesLists\File
 */
class Storage implements IFile
{
    use TMdLang;

    /** @var IKwStorage */
    protected $storage = null;
    /** @var string */
    protected $moduleConfPath = '';
    /** @var string */
    protected $path = '';

    public function __construct(IKwStorage $storage, string $moduleConfPath, ?IMdTranslations $lang = null)
    {
        $this->setMdLang($lang);
        $this->moduleConfPath = $moduleConfPath;
        $this->storage = $storage;
    }

    public function setModuleLevel(int $level): void
    {
        $this->path = Stuff::sanitize(implode(DIRECTORY_SEPARATOR, [
            $this->moduleConfPath,
            sprintf('%s.%d.%s', IPaths::DIR_MODULE, $level, IPaths::DIR_CONF )
        ]));
    }

    public function load(): string
    {
        try {
            $data = $this->storage->read($this->getPath());
            return is_resource($data) ? strval(stream_get_contents($data, -1, 0)) : strval($data);
        } catch (StorageException $ex) {
            throw new ModuleException($this->getMdLang()->mdStorageLoadProblem(), 0, $ex);
        }
    }

    public function save(string $records): bool
    {
        try {
            return $this->storage->write($this->getPath(), $records);
        } catch (StorageException $ex) {
            throw new ModuleException($this->getMdLang()->mdStorageSaveProblem(), 0, $ex);
        }
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
