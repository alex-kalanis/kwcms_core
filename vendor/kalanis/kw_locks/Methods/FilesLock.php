<?php

namespace kalanis\kw_locks\Methods;


use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Traits\TToString;
use kalanis\kw_locks\Interfaces\IKLTranslations;
use kalanis\kw_locks\Interfaces\IPassedKey;
use kalanis\kw_locks\LockException;
use kalanis\kw_locks\Traits\TLang;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\PathsException;


/**
 * Class FilesLock
 * @package kalanis\kw_locks\Methods
 */
class FilesLock implements IPassedKey
{
    use TLang;
    use TToString;

    protected ArrayPath $arrPt;
    protected CompositeAdapter $files;
    /** @var string[] */
    protected array $specialKey = [];
    protected string $checkContent = '';

    public function __construct(CompositeAdapter $files, ?IKLTranslations $lang = null)
    {
        $this->arrPt = new ArrayPath();
        $this->files = $files;
        $this->setKlLang($lang);
    }

    public function __destruct()
    {
        try {
            $this->delete();
        } catch (LockException $ex) {
            // do nothing instead of
            // register_shutdown_function([$this, 'delete']);
        }
    }

    public function setKey(string $key, string $checkContent = ''): void
    {
        try {
            $this->specialKey = $this->arrPt->setString($key)->getArray();
            $this->checkContent = empty($checkContent) ? strval(getmypid()) : $checkContent ;
            // @codeCoverageIgnoreStart
        } catch (PathsException $ex) {
            throw new LockException($this->getKlLang()->iklCannotUsePath($key), $ex->getCode(), $ex);
        }
        // @codeCoverageIgnoreEnd
    }

    public function has(): bool
    {
        try {
            if (!$this->files->exists($this->specialKey)) {
                return false;
            }
            if ($this->checkContent == $this->toString('lock', $this->files->readFile($this->specialKey))) {
                return true;
            }
            throw new LockException($this->getKlLang()->iklLockedByOther());
        } catch (FilesException | PathsException $ex) {
            throw new LockException($this->getKlLang()->iklProblemWithStorage(), $ex->getCode(), $ex);
        }
    }

    public function create(bool $force = false): bool
    {
        if (!$force && $this->has()) {
            return false;
        }
        try {
            $result = $this->files->saveFile($this->specialKey, $this->checkContent);
            return $result;
        } catch (FilesException | PathsException $ex) {
            throw new LockException($this->getKlLang()->iklProblemWithStorage(), $ex->getCode(), $ex);
        }
    }

    public function delete(bool $force = false): bool
    {
        if (!$force && !$this->has()) {
            return true;
        }
        try {
            return $this->files->deleteFile($this->specialKey);
        } catch (FilesException | PathsException $ex) {
            throw new LockException($this->getKlLang()->iklProblemWithStorage(), $ex->getCode(), $ex);
        }
    }
}
