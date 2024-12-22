<?php

namespace KWCMS\modules\Krep\Libs\Add;


use KWCMS\modules\Krep\Libs;


/**
 * Class ProcessPage
 * @package KWCMS\modules\Krep\Libs\Add
 */
class ProcessPage
{
    public function __construct(
        protected readonly Libs\Shared\Query $query,
        protected readonly Libs\Shared\Parser $parser,
    ) {
    }

    /**
     * @param string $addr
     * @throws Libs\ModuleException
     * @return Libs\Shared\PageData
     */
    public function pageData(string $addr): Libs\Shared\PageData
    {
        $response = $this->query->getContent($addr);

        $len = strlen($response->data);
        if ($len < 1) {
            throw new Libs\ModuleException(__('no_connect'), 204);
        } elseif (
            ($len < 60)
            || (!empty($response->headers['response_code'])) && in_array($response->headers['response_code'], [301, 302, 307])
        ) { // bacha na redirect!
            throw new Libs\ModuleException('Partial Content', 206);
        }

        if (strpos($response->data, "<head>")) {
            return $this->parser->process($response->data, true, null);
        }

        throw new Libs\ModuleException('No Content to display', 204);
    }
}
