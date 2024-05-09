<?php

namespace kalanis\kw_clipr\Tasks;


use kalanis\kw_clipr\Interfaces;
use kalanis\kw_clipr\Output;
use kalanis\kw_input\Interfaces\IFiltered;


/**
 * Class ATask
 * @package kalanis\kw_clipr\Tasks
 * @property bool $verbose
 * @property bool $noHeaders
 * @property bool $noColor
 * @property string $outputFile
 * @property bool $webOutput
 * @property bool $noAppend
 * @property bool $quiet
 * @property bool $help
 */
abstract class ATask implements Interfaces\IStatuses
{
    use Output\TWrite;

    protected ?Interfaces\ILoader $loader = null;
    protected Output\AOutput $translator;
    protected Params $params;
    protected IFiltered $inputs;

    /**
     * @param Output\AOutput $translator
     * @param IFiltered $inputs
     * @param Interfaces\ILoader|null $loader
     */
    public final function initTask(Output\AOutput $translator, IFiltered $inputs, ?Interfaces\ILoader $loader): void
    {
        $this->loader = $loader;
        $this->translator = $translator;
        $inputArray = $inputs->getInArray();
        $this->params = new Params($inputArray);
        $this->inputs = $inputs;
        $this->startup();
    }

    protected function startup(): void
    {
        // you can set your variables here
        $this->params->addParam('verbose', 'verbose', null, false, 'v', 'Verbose output');
        $this->params->addParam('noHeaders', 'no-headers', null, true, null, 'No headers from core');
        $this->params->addParam('noColor', 'no-color', null, false, 'c', 'Use no colors in output');
        $this->params->addParam('outputFile', 'output-file', null, Interfaces\ISources::OUTPUT_STD, null, 'Output into...');
        $this->params->addParam('webOutput', 'web-output', null, false, 'w', 'Output is into web');
        $this->params->addParam('noAppend', 'no-append', null, false, null, 'Overwrite whole output');
        $this->params->addParam('quiet', 'quiet', null, false, 'q', 'Silence output');
        $this->params->addParam('help', 'help', null, false, 'h', 'Help with task');
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function __get($name)
    {
        return $this->params->__get($name);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset($name): bool
    {
        return $this->params->__isset($name);
    }

    protected function getTranslator(): Output\AOutput
    {
        return $this->translator;
    }

    protected function getParams(): Params
    {
        return $this->params;
    }

    /**
     * Description of script
     * @return string
     */
    abstract public function desc(): string;

    /**
     * Process script itself
     * @return int status of process
     * @see Interfaces\IStatuses
     */
    abstract public function process(): int;
}
