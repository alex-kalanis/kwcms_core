<?php

namespace KWCMS\modules\Krep\Libs\Shared;


use KWCMS\modules\Krep\Libs;


/**
 * Class ErrorResult
 * @package KWCMS\modules\Krep\Libs\Add
 */
class ErrorResult
{
    public function __construct(
        protected readonly Links $links,
    )
    {
    }

    public function getContent(PageData $pageData, Libs\ModuleException $exception, bool $showThema = false): string
    {
        $displayInfo = new Libs\Template('nosent');
        $displayInfo->change('{PROBLEM}', __("err_problem"));

        switch ($exception->getCode()) {
            case 204:
            case 206:
            case 403:
            case 404:
            case 406:
                // no complete content, banned
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
        }

        if ($prev = $exception->getPrevious()) {
            $parent = new Libs\Template('nosent');
            $parent->change('{PROBLEM}', __("err_prev_problem"));
            $parent->change('{ERRNUM}', $prev->getCode() ?: '?');
            $parent->change('{ERRSTR}', $prev->getMessage() ?: '?');
            $parent->change('{MADE_BY}', '');
            $parent->change('{NOT_SENT}', '');
            $parent->change('{CONTINUE}', '');
            $displayInfo->change('{MADE_BY}', $parent->render());
        } else {
            $displayInfo->change('{MADE_BY}', '');
        }

        $displayInfo->change('{CONTINUE}', $this->links->get($pageData, $showThema));
        return $displayInfo->render();
    }
}
