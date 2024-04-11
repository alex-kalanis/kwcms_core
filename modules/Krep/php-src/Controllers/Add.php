<?php

namespace KWCMS\modules\Krep\Controllers;


use kalanis\kw_bans\BanException;
use kalanis\kw_forms\Exceptions\FormsException;
use KWCMS\modules\Krep\Libs;


/**
 * Class Discus
 * @package KWCMS\modules\Krep\Controllers
 * List discussions
 */
class Add extends ADisposition
{
    /** @var Libs\Add\PostForm */
    protected ?Libs\Add\PostForm $form = null;
    protected Libs\Add\ServerData $serverData;
    protected Libs\Add\ProcessPage $processPage;
    protected Libs\Add\ProcessForm $processForm;
    protected Libs\Shared\PageData $pageData;
    protected ?Libs\ModuleException $error = null;
    protected Libs\Add\BlockResult $blockResult;
    protected Libs\Add\ErrorResult $errorResult;
    protected Libs\Add\RenderFactory $renderFactory;
    protected Libs\Logs\CompositeLogger $logger;

    public function __construct(
        Libs\Config $config,
        Libs\Add\ServerData $serverData,
        Libs\Add\ProcessPage $processPage,
        Libs\Add\ProcessForm $processForm,
        Libs\Shared\PageData $pageData,
        Libs\Add\BlockResult $blockResult,
        Libs\Add\ErrorResult $errorResult,
        Libs\Add\RenderFactory $renderFactory,
        Libs\Logs\CompositeLogger $logger
    ) {
        parent::__construct($config);

        $this->processPage = $processPage;
        $this->processForm = $processForm;
        $this->pageData = $pageData;
        $this->blockResult = $blockResult;
        $this->errorResult = $errorResult;
        $this->renderFactory = $renderFactory;
        $this->serverData = $serverData;
        $this->logger = $logger;
    }

    public function process(): void
    {
        try {
            $this->serverData->setInputs($this->inputs);
            $this->pageData = $this->processPage->pageData(
                strval($this->getFromInput('addr', ''))
            );
            $this->config->site_name = $this->pageData->getTitle();
            $this->form = $this->processForm->processForm($this->inputs, $this->pageData, $this->serverData);

        } catch (Libs\ModuleException | BanException | FormsException $ex) {
            $this->logger->logError(
                $this->serverData,
                $this->pageData,
                strval($this->form->username->getValue()),
                $ex
            );
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

        return $this->blockResult->render($this->renderFactory->whichContent($this->form), $this->pageData);
    }
}
