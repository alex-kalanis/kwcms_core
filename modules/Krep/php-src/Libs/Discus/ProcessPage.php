<?php

namespace KWCMS\modules\Krep\Libs\Discus;


use KWCMS\modules\Krep\Libs;


/**
 * Class ProcessPage
 * @package KWCMS\modules\Krep\Libs\Discus
 */
class ProcessPage
{
    public function __construct(
        protected readonly Libs\Shared\Query $query,
        protected readonly Moved $moved,
        protected readonly Libs\Shared\Parser $parser,
    )
    {
    }

    /**
     * @param string $addr
     * @param string $host
     * @param string $script
     * @param bool $archived
     * @param string $postNumber
     * @throws Libs\ModuleException
     * @return Libs\Shared\PageData
     */
    public function process(string $addr, string $host, string $script, bool $archived, string $postNumber): Libs\Shared\PageData
    {
        $remoteData = $this->query->getContent($addr);

        $len = strlen($remoteData->data);
        if ($len < 1) {
            throw new Libs\ModuleException('No Content', 204);
        } elseif ($len < 60) { // bacha na redirect!
            throw new Libs\ModuleException('Partial Content', 206);
        }

        if (strpos($remoteData->data, 'Litujeme') && strpos($remoteData->data, 'se v diskusi ani archivech od roku')) {
            throw new Libs\ModuleException('No Content found', 204);
        }

        if (strpos($remoteData->data, 'Moved')) {
            return $this->moved->process($remoteData->data, $host, $script);
        }

        if (strpos($remoteData->data, "<head>")) {
            return $this->parser->process(
                $remoteData->data,
                !$archived,
                (!empty($postNumber)) ? intval($postNumber): null
            );
        }

        throw new Libs\ModuleException('No Content for display', 204);
    }
}
