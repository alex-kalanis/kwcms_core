<?php

namespace kalanis\kw_semaphore\Semaphore;


use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_semaphore\Interfaces\ISemaphore;
use kalanis\kw_semaphore\Interfaces\ISMTranslations;
use kalanis\kw_semaphore\SemaphoreException;
use kalanis\kw_semaphore\Traits\TLang;
use kalanis\kw_storage\Interfaces\IStorage;


/**
 * Class Factory
 * @package kalanis\kw_semaphore\Semaphore
 * Data source for semaphore is files
 */
class Factory
{
    use TLang;

    public function __construct(?ISMTranslations $lang = null)
    {
        $this->setSmLang($lang);
    }

    /**
     * @param mixed $params
     * @throws SemaphoreException
     * @return ISemaphore
     */
    public function getSemaphore($params): ISemaphore
    {
        if (is_object($params) && ($params instanceof ISemaphore)) {
            return $params;
        }
        if (is_array($params)) {
            if (isset($params['semaphore'])) {
                if (is_object($params['semaphore'])) {
                    if ($params['semaphore'] instanceof ISemaphore) {
                        return $params['semaphore'];
                    }
                    if ($params['semaphore'] instanceof CompositeAdapter) {
                        return new Files(
                            $params['semaphore'],
                            isset($params['semaphore_root']) && is_array($params['semaphore_root'])
                                ? $params['semaphore_root']
                                : []
                        );
                    }
                    if ($params['semaphore'] instanceof IStorage) {
                        return new Storage(
                            $params['semaphore'],
                            isset($params['semaphore_root']) && is_string($params['semaphore_root'])
                                ? $params['semaphore_root']
                                : ''
                        );
                    }
                }
                if (is_string($params['semaphore'])) {
                    return new Volume($params['semaphore'], $this->getSmLang());
                }
            }
        }
        if (is_string($params)) {
            return new Volume($params, $this->getSmLang());
        }
        throw new SemaphoreException($this->getSmLang()->smCannotGetSemaphoreClass());
    }
}
