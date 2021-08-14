<?php

namespace KWCMS\modules\Admin\Shared;


use kalanis\kw_auth\Interfaces\IUser;
use kalanis\kw_langs\Lang;
use kalanis\kw_modules\Output\Html;
use KWCMS\modules\Admin\Templates\DashboardTemplate;


/**
 * Class FillHtml
 * @package KWCMS\modules\Admin\Shared
 * This output can be filled inside the structure
 */
class FillHtml extends Html
{
    protected $user = null;
    protected $fillTemplate = null;

    public function __construct(?IUser $user)
    {
        Lang::load('Admin');
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
