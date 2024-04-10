<?php

namespace KWCMS\modules\Krep\Libs\Add;


use KWCMS\modules\Krep\Libs;


/**
 * Class ProcessPage
 * @package KWCMS\modules\Krep\Libs\Add
 */
class ProcessPage
{
    protected Libs\Shared\Query $query;
    protected Libs\Shared\Parser $parser;

    public function __construct(
        Libs\Shared\Query $query,
        Libs\Shared\Parser $parser
    ) {
        $this->query = $query;
        $this->parser = $parser;
    }

    /**
     * @param string $addr
     * @throws Libs\ModuleException
     * @return Libs\Shared\PageData
     */
    public function pageData(string $addr): Libs\Shared\PageData
    {
        $remoteData = $this->query->getContent($addr);

        $len = strlen($remoteData);
        if ($len < 1) {
            throw new Libs\ModuleException('No Content', 204);
        } elseif ($len < 60) { // bacha na redirect!
            throw new Libs\ModuleException('Partial Content', 206);
        }

        if (strpos($remoteData, "<head>")) {
            return $this->parser->process($remoteData, true, null);
        }

        throw new Libs\ModuleException('No Content for display', 204);
    }
}
