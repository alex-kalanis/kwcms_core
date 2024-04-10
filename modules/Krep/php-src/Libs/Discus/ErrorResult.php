<?php

namespace KWCMS\modules\Krep\Libs\Discus;


use KWCMS\modules\Krep\Libs;


/**
 * Class ErrorResult
 * @package KWCMS\modules\Krep\Libs\Discus
 */
class ErrorResult
{
    protected Libs\Shared\Links $links;

    public function __construct(Libs\Shared\Links $links)
    {
        $this->links = $links;
    }

    public function getContent(Libs\Shared\PageData $pageData, Libs\ModuleException $exception): string
    {
        $displayInfo = new Libs\Template('nosent');
        $displayInfo->change('{PROBLEM}', __("err_problem"));

        switch ($exception->getCode()) {
            case 204:
            case 206:
                // no complete content
                $displayInfo->change('{ERRNUM}', $exception->getCode());
                $displayInfo->change('{ERRSTR}', $exception->getMessage());
                $displayInfo->change('{NOT_SENT}', __("err_no_sent"));
                break;
            case 503:
                // no connect
                $displayInfo->change('{ERRNUM}', $exception->getCode());
                $displayInfo->change('{ERRSTR}', $exception->getMessage());
                $displayInfo->change('{NOT_SENT}', __("err_no_connect"));
                break;
            default:
                // some undefined problem
                $displayInfo->change('{ERRNUM}', '?');
                $displayInfo->change('{ERRSTR}', '?');
                $displayInfo->change('{NOT_SENT}', __("err_another_exception"));
        };

        $displayInfo->change('{CONTINUE}', $this->links->get($pageData));
        return $displayInfo->render();
    }
}
