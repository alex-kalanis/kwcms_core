<?php

namespace KWCMS\modules\Krep\Libs\Add;


use kalanis\kw_bans\BanException;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_input\Interfaces\IFiltered;
use KWCMS\modules\Krep\Libs;


/**
 * Class ProcessForm
 * @package KWCMS\modules\Krep\Libs\Add
 */
class ProcessForm
{
    public function __construct(
        protected readonly Libs\Config $config,
        protected readonly Libs\Shared\Query $query,
        protected PostForm $form,
        protected readonly Libs\Add\Bans $bans,
        protected readonly Libs\Logs\CompositeLogger $logger,
    ) {
    }

    /**
     * @param IFiltered $filtered
     * @param Libs\Shared\PageData $pageData
     * @param ServerData $serverData
     * @throws BanException
     * @throws FormsException
     * @throws Libs\ModuleException
     * @return PostForm|null null when sent, instance when need to fill
     */
    public function processForm(IFiltered $filtered, Libs\Shared\PageData $pageData, ServerData $serverData): ?PostForm
    {
        $this->form->compose();
        $this->form->setInputs(new InputVarsAdapter($filtered));

        if ($this->form->process()) {
            $this->bans->checkBans(
                $pageData,
                $serverData,
                strval($this->form->getValue('username'))
            );

            $response = $this->query->postToServer(
                'https://www.k-report.net/cgi-bin/discus/board-post.pl',
                $this->query->contextDataForPost(
                    $pageData,
                    $serverData,
                    $this->localToOriginal($pageData, $this->processExternals(strval($this->form->getValue('message')))),
                    $this->localToOriginal($pageData, strval($this->form->getValue('username'))),
                    $this->localToOriginal($pageData, strval($this->form->getValue('passwd'))),
                    $this->localToOriginal($pageData, strval($this->form->getValue('email'))),
                    $this->localToOriginal($pageData, strval($this->form->getValue('url')))
                )
            );

            $code = $this->parseResponseCode($response);
            if (!in_array($code, [200, 302])) {
                throw new Libs\ModuleException('Error during post', $code);
            }

            $this->logger->logPass($serverData, $pageData, strval($this->form->getValue('username')));

            return null;
        }
        return $this->form;
    }

    /**
     * zmena encodingu na to, co chce server
     * @param Libs\Shared\PageData $pageData
     * @param string $value
     * @return string
     */
    protected function localToOriginal(Libs\Shared\PageData $pageData, string $value): string
    {
        $encodingTarget = $this->config->encoding;
        if ($encodingTarget != $pageData->getEncodingRemote()) {
            $value = iconv($encodingTarget, $pageData->getEncodingRemote(), $value);
        }
        return $value;
    }

    /**
     * @param string $message
     * @return string
     */
    protected function processExternals(string $message): string
    {
        $sm = ["happy", "wink", "proud", "lol", "biggrin", "rofl", "talker", "kiss",
            "angry", "crazy", "sad", "uhoh", "coze", "blush", "lame", "yawn", "nene",
            "jidlo", "ok", "ko", "kladivo", "bomba", "cunik", "zadnice",
            "fotic", "vypravci", "masinka", "tramvaj", "auto",
            "andel", "smrt", "satan", "kecal", "blesk", "pozor", "new"];
        foreach ($sm as $i => $nm) {
            $message = str_replace('::' . $nm . '::', '\clipart{' . $nm . '}', $message);
            $message = str_replace('[' . $nm . ']', '\clipart{' . $nm . '}', $message);
        }
        return $message . "\r\n" . $this->config->sign; // oznaceni k odeslanemu prispevku
    }

    /**
     * @param Libs\Shared\ResponseData $response
     * @return int
     */
    protected function parseResponseCode(Libs\Shared\ResponseData $response): int
    {
        return (!empty($response->headers['response_code'])) ? intval($response->headers['response_code']) : 406;
    }
}
