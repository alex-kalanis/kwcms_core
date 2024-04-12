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
$di->setParamNamesForAdditional(['extraParams', 'addedParams']);

// where is the system?
$systemPaths = new \kalanis\kw_paths\Path();
$systemPaths->setDocumentRoot(realpath($_SERVER['DOCUMENT_ROOT']));
$systemPaths->setPathToSystemRoot('/..');
\kalanis\kw_paths\Stored::init($systemPaths);
$di->addClassWithDeepInstances($systemPaths);

// load virtual parts - if exists
$routedPaths = new \kalanis\kw_routed_paths\RoutedPath(new \kalanis\kw_routed_paths\Sources\Server(
    strval(getenv('VIRTUAL_DIRECTORY') ?: 'web/')
));
\kalanis\kw_routed_paths\StoreRouted::init($routedPaths);
$di->addClassWithDeepInstances($routedPaths);

// init config
\kalanis\kw_confs\Config::init(new \kalanis\kw_confs\Loaders\PhpLoader($systemPaths, $routedPaths));
\kalanis\kw_confs\Config::load('Core', 'site'); // autoload core config
\kalanis\kw_confs\Config::load('Core', 'page'); // autoload core config
\kalanis\kw_confs\Config::set('Core', 'site.default_display_module', 'Layout'); // overwrite default display
\kalanis\kw_confs\Config::set('Core', 'page.data_separator', true); // overwrite default display - want use data directory

// init styles and scripts
\kalanis\kw_scripts\Scripts::init(new \kalanis\kw_scripts\Loaders\PhpLoader($systemPaths, $routedPaths));
\kalanis\kw_styles\Styles::init(new \kalanis\kw_styles\Loaders\PhpLoader($systemPaths, $routedPaths));

// pass parsed params as external source
$argv = isset($argv) ? $argv : [] ;
$source = new \kalanis\kw_input\Sources\Basic();
$source->setCli($argv)->setExternal($routedPaths->getArray()); // argv is for params from cli
$inputs = new \kalanis\kw_input\Inputs();
$inputs->setSource($source)->loadEntries();
$di->addClassWithDeepInstances($inputs);
$di->initDeepStoredClass(\kalanis\kw_input\Filtered\Variables::class);

// init langs - the similar way like configs, but it's necessary to already have loaded params
\kalanis\kw_langs\Lang::init(
    new \kalanis\kw_langs\Loaders\PhpLoader($systemPaths, $routedPaths),
    \kalanis\kw_langs\Support::fillFromPaths(
        $routedPaths,
        \kalanis\kw_confs\Config::get('Core', 'page.default_lang', 'hrk'),
        false
    )
);
\kalanis\kw_langs\Lang::load('Core'); // autoload core lang


// And now we have all necessary variables to build the page
try {
    $module = $di->initClass(\KWCMS\modules\Core\Libs\Module::class, [
        'params' => [
            'modules_loaders' => [
                'web',
            ],
            'modules_source' => [
                'modules_param_format' => 'http',
//                'volume_path' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'modules'
                'volume_path' => $systemPaths->getDocumentRoot() . $systemPaths->getPathToSystemRoot() . DIRECTORY_SEPARATOR . \kalanis\kw_paths\Interfaces\IPaths::DIR_MODULE
            ],
            'files' => [
                'path' => $systemPaths->getDocumentRoot() . $systemPaths->getPathToSystemRoot()
            ],
            'module_pedigree' => 'single',
            'socials' => [
                'youtube' => [
                    'width' => 550,
                    'height' => 350,
                ],
                'reddit' => [
                    'sub' => 'czech',
                ],
            ],
            'default_user' => 'data/',
        ]
    ]);
    echo $module->process(['Core'])->get(); // dump output
} catch (\Exception $ex) {
    echo get_class($ex) . ': ' . $ex->getMessage() . ' in ' . $ex->getFile() . ':' . $ex->getLine() . PHP_EOL;
    echo "Stack trace:" . PHP_EOL;
    echo $ex->getTraceAsString() . PHP_EOL;
}
