<?php

namespace KWCMS\modules\Krep\Libs;


/**
 * Class Config
 * @package KWCMS\modules\Krep\Libs
 * @property string $path
 * @property string $site_name
 * @property string $site_link
 * @property string $remote_domain
 * @property string $encoding
 * @property string $sign
 */
class Config
{
    /** @var array<string, string> */
    protected static array $conf = [
        'path' => '/',
        'site_name' => 'K-REPORT MOBILE 3.3',
        'site_link' => 'krep.kalanys.com',
        'remote_domain' => 'www.k-report.net',
        'encoding' => 'utf-8',
        'sign' => '\-2{Posláno z mobilu. 3.3}',
    ];

    /** @var array<string, string> */
    public array $menuLinks = [
        "Železnice" => "/discus/messages/28/28.html",
        "Tramvaje a metro" => "/discus/messages/48/48.html",
        "Autobusy a trolejbusy" => "/discus/messages/2484/2484.html",
        "Auta, lodě a letadla" => "/discus/messages/54342/54342.html",
        "Vzkazy pro redakci" => "/discus/messages/1330/1330.html",
        "Hry a simulátory" => "/discus/messages/238/238.html",
        "Modely a modelářství" => "/discus/messages/3175/3175.html",
        "Inzeráty" => "/discus/messages/237/237.html",
        "Diskuse k článkům" => "/discus/messages/25453/25453.html"
    ];

    public function __construct()
    {
        $this->__set('path', realpath(dirname(__DIR__, 4)) . DIRECTORY_SEPARATOR);
    }

    public function __set($key, $value)
    {
        static::$conf[$key] = $value;
        return $this;
    }

    public function __isset($key)
    {
        return isset(static::$conf[$key]);
    }

    public function __get($key)
    {
        return static::$conf[$key];
    }
}
