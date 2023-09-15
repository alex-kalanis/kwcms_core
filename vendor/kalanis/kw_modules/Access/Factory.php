<?php

namespace kalanis\kw_modules\Access;


use kalanis\kw_files\Access\Factory as files_factory;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\IFLTranslations;
use kalanis\kw_files\Interfaces\IProcessFiles;
use kalanis\kw_modules\Interfaces\ILoader;
use kalanis\kw_modules\Interfaces\IMdTranslations;
use kalanis\kw_modules\Interfaces\Lists\File\IParamFormat;
use kalanis\kw_modules\Interfaces\Lists\IModulesList;
use kalanis\kw_modules\ModuleException;
use kalanis\kw_modules\ModulesLists\File;
use kalanis\kw_modules\ModulesLists\ParamsFormat;
use kalanis\kw_modules\Loaders;
use kalanis\kw_modules\Mixer\Processor;
use kalanis\kw_modules\Traits\TMdLang;
use kalanis\kw_paths\PathsException;
use kalanis\kw_storage\Access\Factory as storage_factory;
use kalanis\kw_storage\Interfaces\IStorage;
use kalanis\kw_storage\Interfaces\IStTranslations;
use kalanis\kw_storage\StorageException;


/**
 * Class Factory
 * @package kalanis\kw_modules\Access
 * Factory to get instances to run
 */
class Factory
{
    use TMdLang;

    public function __construct(?IMdTranslations $lang = null)
    {
        $this->setMdLang($lang);
    }

    /**
     * @param mixed $params
     * @throws ModuleException
     * @return Processor
     */
    public function getProcessor($params): Processor
    {
        return new Processor($this->getLoader($params), $this->getModulesList($params), $this->getMdLang());
    }

    /**
     * @param mixed $params
     * @throws ModuleException
     * @return ILoader
     */
    public function getLoader($params): ILoader
    {
        if (is_object($params)) {
            if ($params instanceof ILoader) {
                return $params;
            }
        }
        if (is_array($params)) {
            if (isset($params['modules_loaders'])) {
                return $this->getLoader($params['modules_loaders']);
            }
            $what = [];
            foreach (array_values($params) as $item) {
                try {
                    $what[] = $this->getLoader($item);
                } catch (ModuleException $ex) {
                    // not found - pass
                }
            }
            return new Loaders\ClassLoader($what);
        }
        if (is_string($params)) {
            try {
                /** @var class-string $params */
                $ref = new \ReflectionClass($params);
                $class = $ref->newInstance();
                if ($class && $class instanceof ILoader) {
                    return $class;
                }
            } catch (\ReflectionException $ex) {
                // nothing
            }
            switch ($params) {
                case 'admin':
                    return new Loaders\Kw\AdminLoader($this->getMdLang());
                case 'api':
                    return new Loaders\Kw\ApiLoader($this->getMdLang());
                case 'web':
                    return new Loaders\Kw\Loader($this->getMdLang());
                case 'di-admin':
                    return new Loaders\KwDi\AdminLoader($this->getMdLang());
                case 'di-api':
                    return new Loaders\KwDi\ApiLoader($this->getMdLang());
                case 'di-web':
                    return new Loaders\KwDi\Loader($this->getMdLang());
            }
        }
        throw new ModuleException($this->getMdLang()->mdNoLoaderSet());
    }

    /**
     * @param mixed $params
     * @throws ModuleException
     * @return IModulesList
     */
    public function getModulesList($params): IModulesList
    {
        if (is_object($params)) {
            if ($params instanceof IModulesList) {
                return $params;
            }
            if ($params instanceof IStorage) {
                return new File(new File\Storage($params, '', $this->getMdLang()), new ParamsFormat\Http());
            }
            if ($params instanceof IProcessFiles) {
                return new File(new File\Files($params, [], $this->getMdLang()), new ParamsFormat\Http());
            }
        }
        if (is_array($params)) {
            if (isset($params['modules_source'])) {
                return $this->getModulesList($params['modules_source']);
            }
            $mdLang = isset($params['modules_lang']) && is_object($params['modules_lang']) && ($params['modules_lang'] instanceof IMdTranslations) ? $params['modules_lang'] : $this->getMdLang();
            $format = isset($params['modules_param_format']) ? $this->getFormat($params['modules_param_format']) : new ParamsFormat\Http();
            try {
                if (isset($params['storage_path'])) {
                    $lang = isset($params['lang']) && is_object($params['lang']) && ($params['lang'] instanceof IStTranslations) ? $params['lang'] : null;
                    return new File(new File\Storage((new storage_factory($lang))->getStorage($params), $params['storage_path'], $mdLang), $format);
                }
                if (isset($params['files_path'])) {
                    $lang = isset($params['lang']) && is_object($params['lang']) && ($params['lang'] instanceof IFLTranslations) ? $params['lang'] : null;
                    return new File(new File\Files((new files_factory($lang))->getClass($params), $params['files_path'], $mdLang), $format);
                }
                if (isset($params['volume_path'])) {
                    return new File(new File\Volume($params['volume_path'], $mdLang), $format);
                }
            } catch (FilesException | PathsException | StorageException $ex) {
                throw new ModuleException($ex->getMessage(), $ex->getCode(), $ex);
            }
        }
        if (is_string($params)) {
            try {
                /** @var class-string $params */
                $ref = new \ReflectionClass($params);
                $class = $ref->newInstance();
                if ($class && $class instanceof IModulesList) {
                    return $class;
                }
            } catch (\ReflectionException $ex) {
                // nothing
            }
        }

        throw new ModuleException($this->getMdLang()->mdNoSourceSet());
    }

    protected function getFormat(string $format): IParamFormat
    {
        switch ($format) {
            case 'serialize':
            case 'serial':
            case 's':
                return new ParamsFormat\Serialize();
            case 'json':
            case 'js':
            case 'j':
                return new ParamsFormat\Json();
            case 'http':
            case 'web':
            case 'w':
            default:
                return new ParamsFormat\Http();
        }
    }
}
