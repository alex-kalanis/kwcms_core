<?php

//// Example bootstrap code for KWCMS

// bootstrap for kwcms 3 - autoloading example
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
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
spl_autoload_register('\kalanis\kw_autoload\Autoload::autoloading');

// where is the system?
$systemPaths = new \kalanis\kw_paths\Path();
$systemPaths->setDocumentRoot(realpath($_SERVER['DOCUMENT_ROOT']));
$systemPaths->setPathToSystemRoot('/..');
\kalanis\kw_paths\Stored::init($systemPaths);

// load virtual parts - if exists
$routedPaths = new \kalanis\kw_routed_paths\RoutedPath(new \kalanis\kw_routed_paths\Sources\Server(
    strval(getenv('VIRTUAL_DIRECTORY') ?: 'dir_from_config/')
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
$source->setCli($argv)->setExternal($params->getParams()); // argv is for params from cli
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
$authenticator = new \kalanis\kw_auth\Sources\Files\Volume\Files(
    new \kalanis\kw_auth\Mode\KwOrig(strval(\kalanis\kw_confs\Config::get('Admin', 'admin.salt'))),
    new \kalanis\kw_auth\Statuses\Always(),
    new \kalanis\kw_locks\Methods\FileLock(
        $systemPaths->getDocumentRoot() . $systemPaths->getPathToSystemRoot() . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . \kalanis\kw_locks\Interfaces\ILock::LOCK_FILE
    ),
    explode(DIRECTORY_SEPARATOR, $systemPaths->getDocumentRoot() . $systemPaths->getPathToSystemRoot() . DIRECTORY_SEPARATOR . 'web')
);
\kalanis\kw_auth\Auth::setAuth($authenticator);
\kalanis\kw_auth\Auth::setGroups($authenticator);
\kalanis\kw_auth\Auth::setClasses($authenticator);
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
                    new \kalanis\kw_auth\Methods\CountedSessions($authenticator,
                        null,
                        $session,
                        100// \kalanis\kw_confs\Config::get('Admin', 'admin.max_log_count', 10)
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


class ExProcessor extends \kalanis\kw_modules\Processing\FileProcessor
{
    public function setModuleLevel(int $level): void
    {
        $this->records = [];
        $this->fileName = implode(DIRECTORY_SEPARATOR, [
            $this->moduleConfPath,
            \kalanis\kw_paths\Interfaces\IPaths::DIR_MODULE,
            sprintf('%s.%d.%s', \kalanis\kw_paths\Interfaces\IPaths::DIR_MODULE, $level, \kalanis\kw_paths\Interfaces\IPaths::DIR_CONF )
        ]);
    }
}


// And now we have all necessary variables to build the page
try {
    $processor = new ExProcessor(
        new \kalanis\kw_modules\Processing\ModuleRecord(),
        $systemPaths->getDocumentRoot() . $systemPaths->getPathToSystemRoot()
    );
    $module = new \kalanis\kw_modules\Module(
        new \kalanis\kw_input\Filtered\Variables($inputs),'',
        new \kalanis\kw_modules\Processing\Modules($processor)
    );
    echo $module->process('Core')->get(); // dump output
} catch (\Exception $ex) {
    echo get_class($ex) . ': ' . $ex->getMessage() . ' in ' . $ex->getFile() . ':' . $ex->getLine() . PHP_EOL;
    echo "Stack trace:" . PHP_EOL;
    echo $ex->getTraceAsString() . PHP_EOL;
}
