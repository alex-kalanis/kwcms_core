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
    protected ?Libs\Add\PostForm $form = null;
    protected ?Libs\ModuleException $error = null;

    public function __construct(
        Libs\Config $config,
        protected readonly Libs\Add\ServerData $serverData,
        protected readonly Libs\Add\ProcessPage $processPage,
        protected readonly Libs\Add\ProcessForm $processForm,
        protected Libs\Shared\PageData $pageData,
        protected readonly Libs\Add\BlockResult $blockResult,
        protected readonly Libs\Add\ErrorResult $errorResult,
        protected readonly Libs\Add\RenderFactory $renderFactory,
        protected readonly Libs\Logs\CompositeLogger $logger,
    ) {
        parent::__construct($config);
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
