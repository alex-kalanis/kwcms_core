<?php

namespace kalanis\kw_input;


use kalanis\kw_input\Interfaces\ISource;


/**
 * Class Inputs
 * @package kalanis\kw_input
 * Base class for passing info from inputs into objects
 * Compress all inputs into single array with entries about everything
 */
class Inputs
{
    /** @var Interfaces\IEntry[] */
    protected array $entries = [];
    protected Interfaces\ISource $source;
    protected Parsers\Factory $parserFactory;
    protected Loaders\Factory $loaderFactory;

    public function __construct(?Parsers\Factory $parserFactory = null, ?Loaders\Factory $loaderFactory = null)
    {
        $this->parserFactory = $parserFactory ?: new Parsers\Factory();
        $this->loaderFactory = $loaderFactory ?: new Loaders\Factory();
        $this->source = new Sources\Basic();
    }

    /**
     * Setting the variable sources - from cli (argv), _GET, _POST, _SERVER, ...
     * @param ISource|string[]|int[]|null $source
     * @return $this
     */
    public function setSource($source = null): self
    {
        if (!empty($source) && ($source instanceof Interfaces\ISource)) {
            $this->source = $source;
        } elseif (($this->source instanceof Sources\Basic) && is_array($source)) {
            $this->source->setCli($source);
        }
        return $this;
    }

    /**
     * Load entries from source into the local entries which will be accessible
     * These two calls came usually in pair
     *
     * $input->setSource($argv)->loadEntries()->getAllEntries();
     * @return $this
     */
    public function loadEntries(): self
    {
        $this->entries = array_merge(
            $this->loadInput(Interfaces\IEntry::SOURCE_EXTERNAL, $this->source->external()),
            $this->loadInput(Interfaces\IEntry::SOURCE_JSON, $this->source->inputRawPaths()),
            $this->loadInput(Interfaces\IEntry::SOURCE_GET, $this->source->get()),
            $this->loadInput(Interfaces\IEntry::SOURCE_POST, $this->source->post()),
            $this->loadInput(Interfaces\IEntry::SOURCE_CLI, $this->source->cli()),
            $this->loadInput(Interfaces\IEntry::SOURCE_COOKIE, $this->source->cookie()),
            $this->loadInput(Interfaces\IEntry::SOURCE_SESSION, $this->source->session()),
            $this->loadInput(Interfaces\IEntry::SOURCE_FILES, $this->source->files()),
            $this->loadInput(Interfaces\IEntry::SOURCE_ENV, $this->source->env()),
            $this->loadInput(Interfaces\IEntry::SOURCE_SERVER, $this->source->server())
        );
        return $this;
    }

    /**
     * @param string $source
     * @param array<string|int, string|int|bool|null|array<string, string|int|bool|null|array<string, string|int|bool|null>>>|null $inputArray
     * @return Interfaces\IEntry[]
     */
    protected function loadInput(string $source, ?array $inputArray = null): array
    {
        if (empty($inputArray)) {
            return [];
        }
        $parser = $this->parserFactory->getLoader($source);
        $loader = $this->loaderFactory->getLoader($source);
        // @phpstan-ignore-next-line
        return $loader->loadVars($source, $parser->parseInput($inputArray));
    }

    /**
     * Get all local entries
     * @return Interfaces\IEntry[] array for foreach
     */
    public function getAllEntries(): array
    {
        return $this->entries;
    }
}
