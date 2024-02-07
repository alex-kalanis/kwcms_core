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
    /** @var Libs\Discus\ProcessPage */
    protected $processor = null;
    /** @var Libs\Shared\PageData */
    protected $pageData = null;
    /** @var Libs\ModuleException|null */
    protected $error = null;
    /** @var Libs\Discus\BlockResult */
    protected $blockResult = null;
    /** @var Libs\Discus\ErrorResult */
    protected $errorResult = null;
    /** @var Libs\Discus\RenderFactory */
    protected $renderFactory = null;

//    /**
//     * @param mixed ...$constructParams
//     */
//    public function __construct(...$constructParams)
//    {
//        parent::__construct(...$constructParams);
//
//        $this->processor = new Libs\Discus\ProcessPage(
//            new Libs\Shared\Query($this->config),
//            new Libs\Discus\Moved(),
//            new Libs\Shared\Parser($this->config)
//        );
//        $this->pageData = new Libs\Shared\PageData();
//        $this->blockResult = new Libs\Discus\BlockResult(new Libs\Shared\Links($this->config), $this->config);
//        $this->errorResult = new Libs\Discus\ErrorResult(new Libs\Shared\Links($this->config));
//        $this->renderFactory = new Libs\Discus\RenderFactory(
//            new Libs\Discus\RenderSinglePost($this->config),
//            new Libs\Discus\RenderTopics($this->config),
//            new Libs\Discus\RenderThemas($this->config)
//        );
//    }

    public function __construct(
        Libs\Config $config,
        Libs\Discus\ProcessPage $processor,
        Libs\Shared\PageData $pageData,
        Libs\Discus\BlockResult $blockResult,
        Libs\Discus\ErrorResult $errorResult,
        Libs\Discus\RenderFactory $renderFactory
    ) {
        parent::__construct($config);

        $this->processor = $processor;
        $this->pageData = $pageData;
        $this->blockResult = $blockResult;
        $this->errorResult = $errorResult;
        $this->renderFactory = $renderFactory;
    }

    public function process(): void
    {
        try {
            $this->pageData = $this->processor->process(
                strval($this->getFromInput('addr', '')),
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
