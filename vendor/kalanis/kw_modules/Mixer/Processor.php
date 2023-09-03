<?php

namespace kalanis\kw_modules\Mixer;


use kalanis\kw_input\Interfaces\IFiltered;
use kalanis\kw_modules\Interfaces\ILoader;
use kalanis\kw_modules\Interfaces\IMdTranslations;
use kalanis\kw_modules\Interfaces\Lists\IModulesList;
use kalanis\kw_modules\Interfaces\Lists\ISitePart;
use kalanis\kw_modules\ModulesLists;
use kalanis\kw_modules\ModuleException;
use kalanis\kw_modules\Parser;


/**
 * Class Processor
 * @package kalanis\kw_modules\Mixer
 * Process modules together
 */
class Processor
{
    /** @var ILoader */
    protected $loader = null;
    /** @var IModulesList */
    protected $files = null;
    /** @var Parser\GetModules */
    protected $parser = null;

    public function __construct(ILoader $loader, IModulesList $files, ?IMdTranslations $lang = null)
    {
        $this->loader = $loader;
        $this->files = $files;
        $this->parser = new Parser\GetModules($lang);
    }

    /**
     * @param string $content
     * @param IFiltered $inputs
     * @param int $level
     * @param array<int, string|int|float|bool> $sharedParams
     * @param array<string, string|int|float|bool|object> $constructParams params passed into __construct
     * @throws ModuleException
     * @return string
     */
    public function fill(string $content, IFiltered $inputs, int $level, array $sharedParams = [], array $constructParams = []): string
    {
        $this->files->setModuleLevel($level);
        $this->parser->setContent($content)->process();

        /** @var ModulesLists\Record[] $availableModules */
        $availableModules = $this->files->listing();
        $willChange = [];
        foreach ($this->parser->getFoundModules() as $item) {
            if (isset($availableModules[$item->getModuleName()])) {
                // known
                if (!$availableModules[$item->getModuleName()]->isEnabled()) {
                    // disabled -> no content to render
                    $willChange[] = $item;
                    continue;
                }

                // known, enabled -> will process
                $module = $this->loader->load($item->getModulePath(), $constructParams);
                if ($module) {
                    $module->init($inputs, array_merge(
                        $sharedParams,
                        $availableModules[$item->getModuleName()]->getParams(),
                        $item->getParams(),
                        [ISitePart::KEY_LEVEL => $level]
                    ));
                    $module->process();
                    $item->setWhatWillReplace($module->output()->output());
                    $willChange[] = $item;
                }
            }
        };

        // now exchange data in $content
        $toChange = (array) array_combine(array_map([$this, 'mapChangeFrom'], $willChange), array_map([$this, 'mapChangeTo'], $willChange));
        return strtr($content, $toChange);
    }

    public function mapChangeFrom(Parser\Record $record): string
    {
        return $record->getToChange();
    }

    public function mapChangeTo(Parser\Record $record): string
    {
        return $record->getWillReplace();
    }
}
