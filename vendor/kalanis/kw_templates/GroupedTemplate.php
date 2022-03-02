<?php

namespace kalanis\kw_templates;


/**
 * Class GroupedTemplate
 * @package kalanis\kw_templates
 * Load external source as template
 */
abstract class GroupedTemplate extends ATemplate
{
    protected static $knownTemplates;

    protected function loadTemplate(): string
    {
        if (empty(static::$knownTemplates)) {
            static::$knownTemplates = $this->defineAvailableTemplates();
        }
        return '';
    }

    /**
     * Define templates available from this class
     * array key is to select one, value is for content
     * @return array
     */
    abstract protected function defineAvailableTemplates(): array;

    protected function resetItems(): self
    {
        $this->items = [];
        return $this;
    }

    /**
     * @param string $key
     * @return $this
     * @throws TemplateException
     * Call only from method in extending class and be prepared for resetting items due unavailability some of them
     */
    protected function selectTemplate(string $key): self
    {
        if (!isset(static::$knownTemplates[$key])) {
            throw new TemplateException(sprintf('Unknown template %s from group %s', $key, get_class($this)));
        }
        $this->setTemplate(static::$knownTemplates[$key]);
        return $this;
    }
}
