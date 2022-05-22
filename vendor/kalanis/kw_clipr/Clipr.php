<?php

namespace kalanis\kw_clipr;


use kalanis\kw_clipr\Interfaces\ISources;
use kalanis\kw_input\Inputs;
use kalanis\kw_input\Variables;


/**
 * Class Clipr
 * @package kalanis\kw_clipr
 * Main class which runs the whole task system
 */
class Clipr
{
    protected $inputs = null;
    protected $variables = null;
    protected $sources = null;
    protected $output = null;

    public function __construct()
    {
        $this->inputs = new Inputs();
        $this->sources = new Clipr\Sources();
        $this->variables = new Variables($this->inputs);
    }

    /**
     * @param string $namespace
     * @param string $path
     * @return $this
     * @throws CliprException
     */
    public function addPath(string $namespace, string $path): self
    {
        Clipr\Paths::getInstance()->addPath($namespace, $path);
        return $this;
    }

    /**
     * @param array $cliArgs
     * @throws CliprException
     */
    public function run(array $cliArgs = []): void
    {
        // void because echo must stay here - we have progress indicator and that needs access to output
        $this->inputs->setSource($cliArgs)->loadEntries();

        $taskFactory = $this->getTaskFactory();
        // for parsing default params it's necessary to load another task
        $dummy = new Tasks\DummyTask();
        $dummy->initTask(new Output\Clear(), $this->variables->getInArray(), $taskFactory);
        $this->sources->determineInput((bool)$dummy->webOutput, (bool)$dummy->noColor);

        // now we know necessary input data, so we can initialize real task
        $inputs = $this->variables->getInArray(null, $this->sources->getEntryTypes());
        $task = $taskFactory->getTask($taskFactory->nthParam($inputs));
        $task->initTask($this->sources->getOutput(), $inputs, $taskFactory);

        if (ISources::OUTPUT_STD != $task->outputFile) {
            ob_start();
        }

        if (false === $task->noHeaders) {
            $task->writeHeader();
        }

        $task->process();

        if (false === $task->noHeaders) {
            $task->writeFooter();
        }

        if (ISources::OUTPUT_STD != $task->outputFile) {
            file_put_contents($task->outputFile, ob_get_clean(), (false === $task->noAppend ? FILE_APPEND : 0));
        }
    }

    public function getTaskFactory(): Tasks\TaskFactory
    {
        return new Tasks\TaskFactory();
    }
}
