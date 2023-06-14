<?php

namespace KWCMS\modules\Notify\AdminControllers;


use kalanis\kw_langs\Lang;
use kalanis\kw_modules\AModule;
use kalanis\kw_modules\Output\AOutput;
use kalanis\kw_modules\Output\Html;
use kalanis\kw_notify\Interfaces\INotify;
use kalanis\kw_notify\Notification;
use kalanis\kw_notify\NotifyException;
use kalanis\kw_scripts\Scripts;
use kalanis\kw_styles\Styles;
use KWCMS\modules\Notify\Template;


/**
 * Class Notify
 * @package KWCMS\modules\Notify\AdminControllers
 * Site's notifications
 */
class Notify extends AModule
{
    protected static $cssClasses = [
        INotify::TARGET_ERROR => 'alert-box-danger',
        INotify::TARGET_WARNING => 'alert-box-warning',
        INotify::TARGET_SUCCESS => 'alert-box-success',
        INotify::TARGET_INFO => 'alert-box-info',
    ];

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
        Styles::want(static::getClassName(static::class), 'notify.css');
        Scripts::want(static::getClassName(static::class), 'notify.js');
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
