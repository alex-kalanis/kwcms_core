<?php

namespace KWCMS\modules\Krep\Controllers;


use kalanis\kw_input\Interfaces\IEntry;
use KWCMS\modules\Krep\Libs;


/**
 * Class Discus
 * @package KWCMS\modules\Krep\Controllers
 * List discussions
 */
class Discus extends ADisposition
{
    protected ?Libs\ModuleException $error = null;

    public function __construct(
        Libs\Config $config,
        protected readonly Libs\Discus\ProcessPage $processor,
        protected Libs\Shared\PageData $pageData,
        protected readonly Libs\Discus\BlockResult $blockResult,
        protected readonly Libs\Shared\ErrorResult $errorResult,
        protected readonly Libs\Discus\RenderFactory $renderFactory,
    ) {
        parent::__construct($config);
    }

    public function process(): void
    {
        try {
            $this->pageData = $this->processor->process(
                strval($this->getFromInput('addr', '')),
                strval($this->getFromInput('REQUEST_SCHEME', 'http', [IEntry::SOURCE_SERVER])),
                strval($this->getFromInput('HTTP_HOST', '', [IEntry::SOURCE_SERVER])),
                strval($this->getFromInput('SCRIPT_NAME', '', [IEntry::SOURCE_SERVER])),
                boolval(intval(strval($this->getFromInput('arch', 0)))),
                strval($this->getFromInput('prisp', ''))
            );
        } catch (Libs\ModuleException $ex) {
            $this->error = $ex;
        }
    }

    protected function getContent(): string
    {
        if ($this->pageData->die) {
            exit();
        }

        if ($this->error) {
            return $this->errorResult->getContent($this->pageData, $this->error);
        }

        return $this->blockResult->render($this->renderFactory->whichContent($this->pageData), $this->pageData);
    }
}
