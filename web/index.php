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
$paths = new \kalanis\kw_paths\Path();
$paths->setDocumentRoot(realpath($_SERVER['DOCUMENT_ROOT']));
$paths->setPathToSystemRoot('/..');

// init config
\kalanis\kw_confs\Config::init($paths);
\kalanis\kw_confs\Config::load('Core', 'site'); // autoload core config
\kalanis\kw_confs\Config::load('Core', 'page'); // autoload core config
\kalanis\kw_confs\Config::load('Admin'); // autoload admin config

// load virtual parts - if exists
$virtualDir = \kalanis\kw_confs\Config::get('Core', 'site.fake_dir', 'dir_from_config/');
$params = new \kalanis\kw_paths\Params\Request\Server();
$params->set($virtualDir)->process();
$paths->setData($params->getParams());

// init langs - the similar way like configs, but it's necessary to already have loaded params
$defaultLang = \kalanis\kw_confs\Config::get('Core', 'page.default_lang', 'hrk');
\kalanis\kw_langs\Lang::init($paths, $defaultLang);
\kalanis\kw_langs\Lang::load('Core'); // autoload core lang

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

// init notifications
\kalanis\kw_notify\Notification::init(
    new \kalanis\kw_notify\Extend\StackName(
        new \kalanis\kw_notify\Stack(
            $session
        ), 'kwadm_'
    )
);

// init styles and scripts
\kalanis\kw_scripts\Scripts::init($paths);
\kalanis\kw_styles\Styles::init($paths);

// authorization tree
$authenticator = new \kalanis\kw_auth\Sources\Files(
    $paths->getDocumentRoot() . $paths->getPathToSystemRoot() . DIRECTORY_SEPARATOR . 'web',
    strval(\kalanis\kw_confs\Config::get('Admin', 'admin.salt'))
);

class ExBanned extends \kalanis\kw_auth\Methods\Banned
{
    protected function getBanPath(): string
    {
        $path = \kalanis\kw_confs\Config::getPath();
        return $path->getDocumentRoot() . $path->getPathToSystemRoot() . DIRECTORY_SEPARATOR . 'web';
    }
}

$handler = new \kalanis\kw_address_handler\Handler(new \kalanis\kw_address_handler\Sources\ServerRequest());
\kalanis\kw_auth\Auth::init(
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
        $server
    )
);
//\kalanis\kw_auth\Auth::init(
//    new \kalanis\kw_auth\Methods\Everytime(null, null)
//);


class ExProcessor extends \kalanis\kw_modules\Processing\FileProcessor
{
    public function setModuleLevel(int $level): void
    {
        $this->records = [];
        $this->fileName = implode(DIRECTORY_SEPARATOR, [
            $this->path->getDocumentRoot() . $this->path->getPathToSystemRoot(),
            \kalanis\kw_paths\Interfaces\IPaths::DIR_MODULE,
            sprintf('%s.%d.%s', \kalanis\kw_paths\Interfaces\IPaths::DIR_MODULE, $level, \kalanis\kw_paths\Interfaces\IPaths::DIR_CONF )
        ]);
    }
}


// And now we have all necessary variables to build the page
$processor = new ExProcessor($paths, new \kalanis\kw_modules\Processing\ModuleRecord());
$module = new \kalanis\kw_modules\Module($inputs, new \kalanis\kw_modules\Processing\Modules($processor));
echo $module->get(); // dump output
