<?php

namespace KWCMS\modules\Krep\Libs\Discus;


use KWCMS\modules\Krep\Libs;


/**
 * Class ProcessPage
 * @package KWCMS\modules\Krep\Libs\Discus
 */
class ProcessPage
{
    /** @var Libs\Shared\Query */
    protected $query = null;
    /** @var Moved */
    protected $moved = null;
    /** @var Libs\Shared\Parser */
    protected $parser = null;

    public function __construct(Libs\Shared\Query $query, Moved $moved, Libs\Shared\Parser $parser)
    {
        $this->query = $query;
        $this->moved = $moved;
        $this->parser = $parser;
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

        $len = strlen($remoteData);
        if ($len < 1) {
            throw new Libs\ModuleException('No Content', 204);
        } elseif ($len < 60) { // bacha na redirect!
            throw new Libs\ModuleException('Partial Content', 206);
        }

        if (strpos($remoteData, 'Litujeme') && strpos($remoteData, 'se v diskusi ani archivech od roku')) {
            throw new Libs\ModuleException('No Content found', 204);
        }

        if (strpos($remoteData, 'Moved')) {
            return $this->moved->process($remoteData, $host, $script);
        }

        if (strpos($remoteData, "<head>")) {
            return $this->parser->process(
                $remoteData,
                !$archived,
                (!empty($postNumber)) ? intval($postNumber): null
            );
        }

        throw new Libs\ModuleException('No Content for display', 204);
    }
}
