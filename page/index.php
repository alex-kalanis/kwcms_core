<?php

//// Example bootstrap code for KWCMS

// bootstrap for kwcms 3 - autoloading example
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
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
spl_autoload_register('\kalanis\kw_autoload\Autoload::autoloading');

// where is the system?
$paths = new \kalanis\kw_paths\Path();
$paths->setDocumentRoot(realpath($_SERVER['DOCUMENT_ROOT']));
$paths->setPathToSystemRoot('/..');

// init config
\kalanis\kw_confs\Config::init(new \kalanis\kw_confs\Loaders\PhpLoader($paths));
\kalanis\kw_confs\Config::load('Core', 'site'); // autoload core config
\kalanis\kw_confs\Config::load('Core', 'page'); // autoload core config
\kalanis\kw_confs\Config::set('Core', 'site.default_display_module', 'Layout');

// load virtual parts - if exists
$virtualDir = \kalanis\kw_confs\Config::get('Core', 'site.fake_dir', 'dir_from_config/');
$params = new \kalanis\kw_paths\Params\Request\Server();
$params->set($virtualDir)->process();
$paths->setData($params->getParams());
// init styles and scripts
\kalanis\kw_scripts\Scripts::init(new \kalanis\kw_scripts\Loaders\PhpLoader($paths));
\kalanis\kw_styles\Styles::init(new \kalanis\kw_styles\Loaders\PhpLoader($paths));

// pass parsed params as external source
$argv = isset($argv) ? $argv : [] ;
$source = new \kalanis\kw_input\Sources\Basic();
$source->setCli($argv)->setExternal($params->getParams()); // argv is for params from cli
$inputs = new \kalanis\kw_input\Inputs();
$inputs->setSource($source)->loadEntries();
\kalanis\kw_paths\Stored::init($paths);

// init langs - the similar way like configs, but it's necessary to already have loaded params
\kalanis\kw_langs\Lang::init(
    new \kalanis\kw_langs\Loaders\PhpLoader($paths),
    \kalanis\kw_langs\Support::fillFromPaths(
        $paths,
        \kalanis\kw_confs\Config::get('Core', 'page.default_lang', 'hrk'),
        false
    )
);
\kalanis\kw_langs\Lang::load('Core'); // autoload core lang


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
        $paths->getDocumentRoot() . $paths->getPathToSystemRoot()
    );
    $module = new \kalanis\kw_modules\Module(
        new \kalanis\kw_input\Variables($inputs),'',
        new \kalanis\kw_modules\Processing\Modules($processor)
    );
    echo $module->process('Core')->get(); // dump output
} catch (\Exception $ex) {
    echo get_class($ex) . ': ' . $ex->getMessage() . ' in ' . $ex->getFile() . ':' . $ex->getLine() . PHP_EOL;
    echo "Stack trace:" . PHP_EOL;
    echo $ex->getTraceAsString() . PHP_EOL;
}
