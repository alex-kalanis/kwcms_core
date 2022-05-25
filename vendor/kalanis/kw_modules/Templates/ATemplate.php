<?php

namespace kalanis\kw_modules\Templates;


use kalanis\kw_confs\Config;
use kalanis\kw_paths\Interfaces\IPaths;
use kalanis\kw_templates\Template\TFile;
use kalanis\kw_templates\TemplateException;


/**
 * Class ATemplate
 * @package kalanis\kw_modules\Templates
 * Basic template system for all module-dependent templates
 * Allows changes in templates done by each user
 */
abstract class ATemplate extends \kalanis\kw_templates\ATemplate
{
    protected static $paths = [
        '%2$s%1$s%3$s%1$s%4$s%1$s%5$s%1$s%6$s%1$s%7$s%1$s%10$s%12$s', // user self-installed modules
        '%2$s%1$s%3$s%1$s%4$s%1$s%5$s%1$s%6$s%1$s%7$s%1$s%10$s%13$s',
        '%2$s%1$s%3$s%1$s%4$s%1$s%7$s%1$s%8$s%1$s%10$s%12$s', // user - direct styles
        '%2$s%1$s%3$s%1$s%4$s%1$s%7$s%1$s%8$s%1$s%10$s%13$s',
        '%2$s%1$s%3$s%1$s%4$s%1$s%7$s%1$s%9$s%1$s%9$s%12$s',
        '%2$s%1$s%3$s%1$s%4$s%1$s%7$s%1$s%9$s%1$s%9$s%13$s',
        '%2$s%1$s%5$s%1$s%6$s%1$s%7$s%1$s%10$s%12$s', // module - tmpl dir
        '%2$s%1$s%5$s%1$s%6$s%1$s%7$s%1$s%10$s%13$s',
        '%2$s%1$s%7$s%1$s%8$s%1$s%10$s%12$s', // style as tmpl dir
        '%2$s%1$s%7$s%1$s%8$s%1$s%10$s%13$s',
        '%2$s%1$s%7$s%1$s%9$s%1$s%9$s%12$s',
        '%2$s%1$s%7$s%1$s%9$s%1$s%9$s%13$s',
        '%2$s%1$s%7$s%1$s%9$s%1$s%11$s%12$s',
        '%2$s%1$s%7$s%1$s%9$s%1$s%11$s%13$s',
    ];

    protected $moduleName = '';
    protected $templateName = '';

    use TFile;

    /**
     * @return string
     * @throws TemplateException
     */
    protected function templatePath(): string
    {
        $documentRoot = Config::getPath()->getDocumentRoot() . Config::getPath()->getPathToSystemRoot();
        $userDir = Config::getPath()->getUser() ? Config::getPath()->getUser() : '' ;
        $defaultStyle = Config::get('Core', 'page.default_style');
        foreach (static::$paths as $path) {
            $path = sprintf($path,
                DIRECTORY_SEPARATOR, $documentRoot,
                IPaths::DIR_USER, $userDir,
                IPaths::DIR_MODULE, $this->moduleName,
                IPaths::DIR_STYLE, $defaultStyle,
                'default', $this->getTemplateName(),
                'dummy', '.htm', '.html');
            if ($clearPath = realpath($path)) {
                return $clearPath;
            }
        }
        throw new TemplateException(sprintf('Unknown template for configuration *%s* - *%s*', $this->moduleName, $this->getTemplateName()));
    }

    public function getTemplateName(): string
    {
        return $this->templateName;
    }
}
