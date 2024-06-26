<?php

namespace KWCMS\modules\Notify\AdminControllers;


use kalanis\kw_langs\Lang;
use kalanis\kw_modules\Output\AOutput;
use kalanis\kw_modules\Output\Html;
use kalanis\kw_notify\Interfaces\INotify;
use kalanis\kw_notify\Notification;
use kalanis\kw_notify\NotifyException;
use KWCMS\modules\Core\Libs\AModule;
use KWCMS\modules\Notify\Template;


/**
 * Class Notify
 * @package KWCMS\modules\Notify\AdminControllers
 * Site's notifications
 */
class Notify extends AModule
{
    /** @var array<string, string> */
    protected static array $cssClasses = [
        INotify::TARGET_ERROR => 'alert-box-danger',
        INotify::TARGET_WARNING => 'alert-box-warning',
        INotify::TARGET_SUCCESS => 'alert-box-success',
        INotify::TARGET_INFO => 'alert-box-info',
    ];

    public function __construct(...$constructParams)
    {
    }

    public function process(): void
    {
//        Notification::addError('Notifications loaded 1.');
//        Notification::addWarning('Notifications loaded 2.');
//        Notification::addSuccess('Notifications loaded 3.');
//        Notification::addInfo('Notifications loaded 4.');
    }

    /**
     * @throws NotifyException
     * @return AOutput
     */
    public function output(): AOutput
    {
        $tmpl = new Template();
        $notifications = [];
        if (Notification::getNotify()) {
            foreach (static::$cssClasses as $type => $cssClass) {
                foreach (Notification::getNotify()->get($type) as $message) {
                    $notifications[] = $tmpl->reset()->setData(Lang::get($type), $message, $cssClass)->render();
                }
                Notification::getNotify()->reset($type);
            }
        }
        $out = new Html();
        return $out->setContent(implode('', $notifications));
    }
}
