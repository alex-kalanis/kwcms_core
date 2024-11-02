<?php

namespace KWCMS\modules\Krep\Libs\Shared;


class ResponseData
{
    /**
     * @param array{
     *      response_code?: int,
     *      last-modified?: string,
     *      accept-ranges?: string,
     *      cache-control?: string,
     *      expires?: string,
     *      content-length?: int,
     *      content-type?: string,
     *      server?: string
     * } $headers
     * @param string|null $data
     */
    public function __construct(
        public readonly array $headers = [],
        public readonly ?string $data = null,
    )
    {
    }
}
