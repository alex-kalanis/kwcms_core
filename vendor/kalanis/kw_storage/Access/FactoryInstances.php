<?php

namespace kalanis\kw_storage\Access;


use kalanis\kw_storage\Interfaces;


/**
 * Class FactoryInstances
 * @package kalanis\kw_storage\Access
 * Factory instances for getting each wanted storage
 */
class FactoryInstances
{
    protected static ?FactoryInstances $instance = null;
    protected Factory $factory;

    public static function init(?Interfaces\IStTranslations $lang = null): void
    {
        static::$instance = new self($lang);
    }

    public static function getInstance(): Factory
    {
        if (empty(static::$instance)) {
            static::$instance = new self();
        }
        return static::$instance->factory;
    }

    protected function __construct(?Interfaces\IStTranslations $lang = null)
    {
        $this->factory = new Factory($lang);
    }

    /**
     * @codeCoverageIgnore singleton
     */
    private function __clone()
    {
        // cannot run
    }
}
