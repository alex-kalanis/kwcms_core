<?php

namespace KWCMS\modules\Admin\Shared;


use kalanis\kw_auth\Interfaces\IUser;
use kalanis\kw_modules\Output\Html;
use KWCMS\modules\Admin\Templates\DashboardTemplate;


/**
 * Class FillHtml
 * @package kalanis\kw_modules
 * This output can be filled inside the structure
 */
class FillHtml extends Html
{
    protected $user = null;
    protected $fillTemplate = null;

    public function __construct(?IUser $user)
    {
        $this->user = $user;
        $this->fillTemplate = new DashboardTemplate();
    }

    public function output(): string
    {
        if (empty($this->user)) {
            return parent::output();
        } else {
            return $this->fillTemplate->reset()->setData($this->user, parent::output())->render();
        }
    }
}
