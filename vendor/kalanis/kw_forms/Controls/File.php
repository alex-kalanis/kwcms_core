<?php

namespace kalanis\kw_forms\Controls;


use kalanis\kw_forms\Exceptions\EntryException;
use kalanis\kw_input\Interfaces\IFileEntry;
use kalanis\kw_rules\Interfaces;
use kalanis\kw_rules\Rules;


/**
 * Class File
 * @package kalanis\kw_forms\Controls
 * Render input for sending files
 * Implementing IValidateFile because kw_rules are really independent
 */
class File extends AControl implements Interfaces\IValidateFile
{
    protected $templateInput = '<input type="file"%2$s />';

    /** @var IFileEntry|null */
    protected $value = null;

    protected function whichFactory(): Interfaces\IRuleFactory
    {
        return new Rules\File\Factory();
    }

    public function set(string $key, string $label = ''): self
    {
        $this->setEntry($key, null, $label);
        $this->setAttribute('id', $this->getKey());
        return $this;
    }

    public function renderInput($attributes = null): string
    {
        $this->addAttributes($attributes);
        $this->setAttribute('name', $this->getKey());
        return $this->wrapIt(sprintf($this->templateInput, null, $this->renderAttributes()), $this->wrappersInput);
    }

    public function setValue($value): void
    {
        if ($value instanceof IFileEntry) {
            $this->value = $value;
        }
    }

    public function getValue()
    {
        $this->checkFile();
        return $this->value->getValue();
    }

    public function getMimeType(): string
    {
        $this->checkFile();
        return $this->value->getMimeType();
    }

    public function getTempName(): string
    {
        $this->checkFile();
        return $this->value->getTempName();
    }

    public function getError(): int
    {
        $this->checkFile();
        return $this->value->getError();
    }

    public function getSize(): int
    {
        $this->checkFile();
        return $this->value->getSize();
    }

    public function getFile(): IFileEntry
    {
        $this->checkFile();
        return $this->value;
    }

    protected function checkFile(): void
    {
        if (empty($this->value)) {
            throw new EntryException(sprintf('Entry %s does not contains file', $this->getKey()));
        }
    }
}
