<?php

//// Example bootstrap code for KWCMS

// bootstrap for kwcms 3 - autoloading example
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
// @link http://web.kwcms_core.lemp.test/          for vagrant
// @link http://web.kwcms_core.lemp.local:20000/   for docker

require_once(__DIR__ . implode(DIRECTORY_SEPARATOR, ['', '..', 'vendor', 'kalanis', 'kw_autoload', 'Autoload.php']));

\kalanis\kw_autoload\Autoload::setBasePath(realpath(__DIR__ . DIRECTORY_SEPARATOR . '..'));
// maybe looks like magic, but it is not
\kalanis\kw_autoload\Autoload::addPath('%2$s%1$svendor%1$s%3$s%1$s%4$s%1$sphp-src%1$s%5$s%1$s%6$s');
\kalanis\kw_autoload\Autoload::addPath('%2$s%1$svendor%1$s%3$s%1$s%4$s%1$s%5$s%1$sphp-src%1$s%6$s');
\kalanis\kw_autoload\Autoload::addPath('%2$s%1$svendor%1$s%3$s%1$s%4$s%1$ssrc%1$s%5$s%1$s%6$s');
\kalanis\kw_autoload\Autoload::addPath('%2$s%1$svendor%1$s%3$s%1$s%4$s%1$s%5$s%1$s%6$s');
\kalanis\kw_autoload\Autoload::addPath('%2$s%1$svendor%1$s%4$s%1$s%5$s%1$sphp-src%1$s%6$s');
\kalanis\kw_autoload\Autoload::addPath('%2$s%1$svendor%1$s%4$s%1$s%5$s%1$s%6$s');
\kalanis\kw_autoload\Autoload::addPath('%2$s%1$svendor%1$s%3$s%1$s%5$s%1$s%6$s');
\kalanis\kw_autoload\Autoload::addPath('%2$s%1$smodules%1$s%4$s%1$s%5$s%1$s%6$s');
\kalanis\kw_autoload\Autoload::addPath('%2$s%1$smodules%1$s%5$s%1$s%6$s');
\kalanis\kw_autoload\Autoload::addPath('%2$s%1$smodules%1$s%6$s');
\kalanis\kw_autoload\Autoload::addPath('%2$s%1$s%5$s%1$s%6$s');

// to modules
\kalanis\kw_autoload\Autoload::addPath('%2$s%1$smodules%1$s%5$s%1$sphp-src%1$s%6$s');
\kalanis\kw_autoload\Autoload::addPath('%2$s%1$smodules%1$s%5$s%1$ssrc%1$s%6$s');
\kalanis\kw_autoload\Autoload::addPath('%2$s%1$smodules%1$s%5$s%1$s%6$s');

spl_autoload_register('\kalanis\kw_autoload\Autoload::autoloading');

// where is the system?
$systemPaths = new \kalanis\kw_paths\Path();
$systemPaths->setDocumentRoot(realpath($_SERVER['DOCUMENT_ROOT']));
$systemPaths->setPathToSystemRoot('/..');
\kalanis\kw_paths\Stored::init($systemPaths);

// load virtual parts - if exists
$routedPaths = new \kalanis\kw_routed_paths\RoutedPath(new \kalanis\kw_routed_paths\Sources\Server(
    strval(getenv('VIRTUAL_DIRECTORY') ?: 'web/')
));
\kalanis\kw_routed_paths\StoreRouted::init($routedPaths);

// init config
\kalanis\kw_confs\Config::init(new \kalanis\kw_confs\Loaders\PhpLoader($systemPaths, $routedPaths));
\kalanis\kw_confs\Config::load('Core', 'site'); // autoload core config
\kalanis\kw_confs\Config::load('Core', 'page'); // autoload core config
\kalanis\kw_confs\Config::load('Admin'); // autoload admin config

session_start();

// pass parsed params as external source
$argv = isset($argv) ? $argv : [] ;
$source = new \kalanis\kw_input\Sources\Basic();
$source->setCli($argv)->setExternal($routedPaths->getArray()); // argv is for params from cli
$inputs = new \kalanis\kw_input\Inputs();
$inputs->setSource($source)->loadEntries();
$session = new \kalanis\kw_input\Simplified\SessionAdapter();
$server = new \kalanis\kw_input\Simplified\ServerAdapter();

// init cookies
\kalanis\kw_input\Simplified\CookieAdapter::init('', '/', 3600);

// init langs - the similar way like configs, but it's necessary to already have loaded params
\kalanis\kw_langs\Lang::init(
    new \kalanis\kw_langs\Loaders\PhpLoader($systemPaths, $routedPaths),
    \kalanis\kw_langs\Support::fillFromPaths(
        $routedPaths,
        \kalanis\kw_langs\Support::fillFromArray(
            $session,
            \kalanis\kw_confs\Config::get('Core', 'page.default_lang', 'hrk')
        ),
        false
    )
);
\kalanis\kw_langs\Lang::load('Core'); // autoload core lang

// init notifications
\kalanis\kw_notify\Notification::init(
    new \kalanis\kw_notify\Extend\StackName(
        new \kalanis\kw_notify\Stack(
            $session
        ), 'kwadm_'
    )
);

// init styles and scripts
\kalanis\kw_scripts\Scripts::init(new \kalanis\kw_scripts\Loaders\PhpLoader($systemPaths, $routedPaths));
\kalanis\kw_styles\Styles::init(new \kalanis\kw_styles\Loaders\PhpLoader($systemPaths, $routedPaths));

// authorization tree
$authStorage = new \kalanis\kw_auth_sources\Sources\Files\Storages\Volume(
    $systemPaths->getDocumentRoot() . $systemPaths->getPathToSystemRoot() . DIRECTORY_SEPARATOR
);
$authParser = new \kalanis\kw_auth_sources\ExtraParsers\Json();
$authLock = new \kalanis\kw_locks\Methods\FileLock(
    $systemPaths->getDocumentRoot() . $systemPaths->getPathToSystemRoot() . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . \kalanis\kw_locks\Interfaces\ILock::LOCK_FILE
);
$authenticator = new \kalanis\kw_auth_sources\Sources\Files\AccountsMultiFile(
    $authStorage,
    new \kalanis\kw_auth_sources\Hashes\KwOrig(strval(\kalanis\kw_confs\Config::get('Admin', 'admin.salt'))),
    new \kalanis\kw_auth_sources\Statuses\Always(),
    $authParser,
    $authLock,
    ['web']
);
\kalanis\kw_auth\Auth::setAuth($authenticator);
\kalanis\kw_auth\Auth::setGroups(new \kalanis\kw_auth_sources\Sources\Files\Groups(
    $authStorage,
    $authenticator,
    $authParser,
    $authLock,
    ['web']
));
\kalanis\kw_auth\Auth::setClasses(new \kalanis\kw_auth_sources\Sources\Classes());
\kalanis\kw_auth\Auth::setAccounts($authenticator);


class ExBanned extends \kalanis\kw_auth\Methods\Banned
{
    protected function getBanPath(): string
    {
        $path = \kalanis\kw_paths\Stored::getPath();
        return $path->getDocumentRoot() . $path->getPathToSystemRoot() . DIRECTORY_SEPARATOR . 'web';
    }
}

$handler = new \kalanis\kw_address_handler\Handler(new \kalanis\kw_address_handler\Sources\ServerRequest());
\kalanis\kw_auth\Auth::fill(
    new ExBanned($authenticator,
        new \kalanis\kw_auth\Methods\HttpCerts($authenticator,
            new \kalanis\kw_auth\Methods\UrlCerts($authenticator,
                new \kalanis\kw_auth\Methods\TimedSessions($authenticator,
                    new \kalanis\kw_auth\Methods\Sessions($authenticator,
//                    new \kalanis\kw_auth\Methods\CountedSessions($authenticator,
                        null,
                        $session,
                        $server
//                        $server,
//                        100// \kalanis\kw_confs\Config::get('Admin', 'admin.max_log_count', 10)
                    ),
                    $session,
                    $server
                ),
                $handler
            ),
            $handler,
            $server
        ),
        $systemPaths,
        $server
    )
);
//\kalanis\kw_auth\Auth::fill(
//    new \kalanis\kw_auth\Methods\Everytime(null, null)
//);


// And now we have all necessary variables to build the page
try {
    $module = new \KWCMS\modules\Core\Libs\Module(new \kalanis\kw_input\Filtered\Variables($inputs), [
        'modules_loaders' => [
            'admin',
//            'api',
            'web',
        ],
        'modules_source' => [
            'modules_param_format' => 'http',
//            'volume_path' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'modules'
            'volume_path' => $systemPaths->getDocumentRoot() . $systemPaths->getPathToSystemRoot() . DIRECTORY_SEPARATOR . \kalanis\kw_paths\Interfaces\IPaths::DIR_MODULE
        ],
        'files' => [
            'path' => $systemPaths->getDocumentRoot() . $systemPaths->getPathToSystemRoot()
//            'path' => $systemPaths->getDocumentRoot() . $systemPaths->getPathToSystemRoot() . DIRECTORY_SEPARATOR . \kalanis\kw_paths\Interfaces\IPaths::DIR_USER
        ]
    ]);
    echo $module->process(['Core'])->get(); // dump output
} catch (\Exception $ex) {
    echo get_class($ex) . ': ' . $ex->getMessage() . ' in ' . $ex->getFile() . ':' . $ex->getLine() . PHP_EOL;
    echo "Stack trace:" . PHP_EOL;
    echo $ex->getTraceAsString() . PHP_EOL;
}
