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
    protected string $templateInput = '<input type="file"%2$s />';
    protected string $errorEntryNotFile = 'Entry %s does not contain a file';
    protected ?IFileEntry $entry = null;

    protected function whichRulesFactory(): Interfaces\IRuleFactory
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

    /**
     * @param bool|float|int|string|IFileEntry|null $value
     */
    public function setValue($value): void
    {
        if ($value instanceof IFileEntry) {
            $this->entry = $value;
        }
    }

    /**
     * @throws EntryException
     * @return bool|float|int|mixed|string|null
     */
    public function getValue()
    {
        return $this->getFile()->getValue();
    }

    /**
     * @throws EntryException
     * @return string
     */
    public function getMimeType(): string
    {
        return $this->getFile()->getMimeType();
    }

    /**
     * @throws EntryException
     * @return string
     */
    public function getTempName(): string
    {
        return $this->getFile()->getTempName();
    }

    /**
     * @throws EntryException
     * @return int
     */
    public function getError(): int
    {
        return $this->getFile()->getError();
    }

    /**
     * @throws EntryException
     * @return int
     */
    public function getSize(): int
    {
        return $this->getFile()->getSize();
    }

    /**
     * @throws EntryException
     * @return IFileEntry
     */
    public function getFile(): IFileEntry
    {
        if (empty($this->entry)) {
            throw new EntryException(sprintf($this->errorEntryNotFile, $this->getKey()));
        }
        return $this->entry;
    }
}
