<?php

namespace kalanis\kw_modules\Mixer\PassedParams;


use kalanis\kw_modules\Interfaces\IMdTranslations;
use kalanis\kw_modules\Interfaces\IModule;
use kalanis\kw_modules\ModuleException;
use kalanis\kw_modules\Traits\TMdLang;
use ReflectionClass;
use ReflectionException;


/**
 * Class Factory
 * @package kalanis\kw_modules\Mixer\PassedParams
 * Which way the params will be transformed?
 */
class Factory
{
    use TMdLang;

    /**
     * @var array<string, class-string<APassedParam>>
     */
    protected array $map = [
        'http' => HttpQuery::class,
        'single' => SingleParam::class,
    ];

    public function __construct(?IMdTranslations $lang = null)
    {
        $this->setMdLang($lang);
    }

    /**
     * @param IModule $module
     * @throws ModuleException
     * @return APassedParam
     */
    public function getClass(IModule $module): APassedParam
    {
        if (method_exists($module, 'passParamsAs')) {
            $passAs = strval($module->passParamsAs());
            try {
                return $this->getLoaded($passAs);
            } catch (ReflectionException $ex) {
                // nothing
            }
            try {
                return $this->getLoaded($this->fromMap($passAs));
            } catch (ReflectionException $ex) {
                // nothing
            }
            throw new ModuleException($this->getMdLang()->mdNoModuleFound());
        }
        try {
            return $this->getLoaded($this->fromMap('http'));
        } catch (ReflectionException $ex) {
            throw new ModuleException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    protected function fromMap(string $passAs): ?string
    {
        if (isset($this->map[$passAs])) {
            return $this->map[$passAs];
        }
        return null;
    }

    /**
     * @param string|null $className
     * @throws ReflectionException
     * @return APassedParam
     */
    protected function getLoaded(?string $className): APassedParam
    {
        if ($className) {
            /** @var class-string $className */
            $ref = new ReflectionClass($className);
            $class = $ref->newInstance();
            if ($class && $class instanceof APassedParam) {
                return $class;
            }
        }
        throw new ReflectionException($this->getMdLang()->mdNoModuleFound());
    }
}
