<?php

//// Example bootstrap code for KWCMS

// bootstrap for kwcms 4 - autoloading example
if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'exterr.php')) {
    require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'exterr.php';
}
if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'errors.php')) {
    require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'errors.php';
}
if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'errors_display.php')) {
    require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'errors_display.php';
}
// presentation

require_once(__DIR__ . implode(DIRECTORY_SEPARATOR, ['', '..', 'vendor', 'kalanis', 'kw_autoload', 'Autoload.php']));

/// Use following:

\kalanis\kw_autoload\Autoload::setBasePath(realpath(__DIR__ . DIRECTORY_SEPARATOR . '..'));
// maybe looks like magic, but it is not
\kalanis\kw_autoload\Autoload::addPath('%2$s%1$svendor%1$s%3$s%1$s%4$s%1$sphp-src%1$s%5$s%1$s%6$s');
\kalanis\kw_autoload\Autoload::addPath('%2$s%1$svendor%1$s%3$s%1$s%4$s%1$s%5$s%1$sphp-src%1$s%6$s');
\kalanis\kw_autoload\Autoload::addPath('%2$s%1$svendor%1$s%3$s%1$s%4$s%1$ssrc%1$s%5$s%1$s%6$s');
\kalanis\kw_autoload\Autoload::addPath('%2$s%1$svendor%1$s%3$s%1$s%4$s%1$s%5$s%1$s%6$s');
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

$di = \kalanis\kw_autoload\DependencyInjection::getInstance();

// where is the system?
$systemPaths = new \kalanis\kw_paths\Path();
$systemPaths->setDocumentRoot(realpath($_SERVER['DOCUMENT_ROOT']));
$systemPaths->setPathToSystemRoot('/..');
\kalanis\kw_paths\Stored::init($systemPaths);
$di->addClassWithDeepInstances($systemPaths);

// pass parsed params as external source
$argv = isset($argv) ? $argv : [] ;
$source = new \kalanis\kw_input\Sources\Basic();
$source->setCli($argv); // argv is for params from cli
$inputs = new \kalanis\kw_input\Inputs();
$inputs->setSource($source)->loadEntries();
$di->addClassWithDeepInstances($inputs);
$di->initDeepStoredClass(\kalanis\kw_input\Filtered\Variables::class);


// translations
if (!function_exists('__')) {
    function __($key){
        return \KWCMS\modules\Krep\Libs\Translations::tr($key);
    }
}

// And now we have all necessary variables to build the page
$module = $di->initClass(\KWCMS\modules\Krep\Libs\Module::class, [
    'params' => [
        'bans_path' => __DIR__ . DIRECTORY_SEPARATOR . '_data' . DIRECTORY_SEPARATOR . 'bany' . DIRECTORY_SEPARATOR,
        'logs_path' => __DIR__ . DIRECTORY_SEPARATOR . '_data' . DIRECTORY_SEPARATOR . 'logy' . DIRECTORY_SEPARATOR,
        'modules_loaders' => [
            'di-web',
//            'web',
        ],
    ]
]);
