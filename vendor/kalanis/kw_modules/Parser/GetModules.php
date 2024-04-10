<?php

namespace kalanis\kw_modules\Parser;


use kalanis\kw_modules\Interfaces\IMdTranslations;
use kalanis\kw_modules\ModuleException;
use kalanis\kw_modules\Support;
use kalanis\kw_modules\Traits\TMdLang;


/**
 * Class GetModules
 * @package kalanis\kw_modules\Parser
 * Parse source template to get submodules
 *
 * No params:
 * In: {MODULE/}
 *
 * Params passed:
 * In: {MODULE}param1=value1&param2=value2&...{/MODULE}
 * In: {OUR-MODULE}param1=value1&param2=value2&...{/OUR-MODULE}
 * In: {MODULE--SUBPART}param1=value1&param2=value2&...{/MODULE--SUBPART}
 *
 * Disabled ones - will be skipped:
 * In: {-MODULE/}
 * In: {!MODULE/}
 * In: {-MODULE}param1=value1&param2=value2&...{/MODULE-}
 *
 * Out: available modules to process
 *
 * After getting modules in that content it's necessary to run them through loaders and replace the content back in source
 * This also use hashed params - so each instance with the same params will be returned just once
 *
 * Intentionally without multibyte calls (mb_*)
 *
 * todo: in this version you cannot make the module which will pass its result as param for another module
 */
class GetModules
{
    use TMdLang;

    protected string $content = '';
    /** @var array<string, Record> */
    protected array $foundModules = [];

    public function __construct(?IMdTranslations $lang = null)
    {
        $this->setMdLang($lang);
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @throws ModuleException
     * @return $this
     */
    public function process(): self
    {
        $this->foundModules = [];
        // regexp got all available module tags on page, strpos their positions and then we van get the params
        if (!preg_match_all('#{([a-zA-Z_!/-]+)}#mi', $this->content,  $matches)) {
            return $this;
        };

        $use = $this->rePair($matches);

        // matches got everything - even solo, ending and disabled
        // so filter them
        $use = array_filter($use, [$this, 'filterUsable']);

        // then add real positions in main string to usable tags
        // due option that tags will be more than once, it must get foreach and start skip
        /** @var Positioned[] $positions */
        $positions = [];
        $start = 0;
        foreach ($use as $found) {
            $pos = strpos($this->content, $found->getInner(), $start);
            if (false !== $pos) {
                $positions[] = new Positioned($found->getBraced(), $found->getInner(), $pos - 1);
                // skip part with already found
                $start = $pos + strlen($found->getInner());
            }
        }

//print_r(['pos', $positions]);
        // then solos can go permanently out
        array_map([$this, 'filteredSoloIntoRecord'], array_filter($positions, [$this, 'filterSolo']));
        /** @var Positioned[] $rest */
        $rest = array_values(array_filter($positions, [$this, 'filterNoSolo'])); // remove holes in indexes

        $stack = new \SplStack();
        $len = count($rest);
        for ($i=0; $i<$len; $i++) {
            if (!$this->filterEndingTag($rest[$i])) {
                $stack[] = $rest[$i];
            } else {
                if ($stack->isEmpty()) {
                    throw new ModuleException($this->getMdLang()->mdNoOpeningTag());
                }
                // check if it's ending for that tag
                /** @var Positioned $top */
                $top = $stack->top();
                $clear = Support::clearModuleName($rest[$i]->getInner());
                if ($top->getInner() != $clear) {
                    // todo: now just lookup for top one, in future try to look deeper if the correct match is somewhere there
                    throw new ModuleException($this->getMdLang()->mdNoEndingTag($top->getInner()));
                }
                $stack->pop();

                // between start and end are the necessary params
                $paramStart = $top->getPos() + strlen($top->getBraced());
                $paramLen = $rest[$i]->getPos() - $paramStart;
                $changeLen = $rest[$i]->getPos() + strlen($rest[$i]->getBraced()) - $top->getPos();

                $parts = Support::modulePathFromTemplate(Support::clearModuleName($top->getInner()));
                $readInternals = substr($this->content, $paramStart, $paramLen);
                $toChange = substr($this->content, $top->getPos(), $changeLen);
//print_r(['proc', $parts, $readParams, $toChange]);
                $rec = new Record();
                $rec->setContent($readInternals);
                $rec->setModuleName(strval(reset($parts)));
                $rec->setModulePath($parts);
                $rec->setContentToChange($toChange);

                $this->foundModules[$this->makeHash($rec)] = $rec;
            }
        }

        return $this;
    }

    /**
     * @param array<int, array<int, string>> $in
     * @return Positioned[]
     * To let data remove just by one index
     */
    protected function rePair(array $in): array
    {
        $pairs = [];
        $match = (array) reset($in);
        foreach ($match as $index => $value) {
            $pairs[] = new Positioned($in[0][$index], $in[1][$index]);
        }
        return $pairs;
    }

    public function filterUsable(Positioned $entry): bool
    {
        $firstLetter = mb_substr($entry->getInner(), 0, 1);
        $lastLetter = mb_substr($entry->getInner(), -1, 1);
        return '-' != $firstLetter && '!' != $firstLetter && '-' != $lastLetter;
    }

    public function filterSolo(Positioned $entry): bool
    {
        $lastLetter = mb_substr($entry->getInner(), -1, 1);
        return '/' == $lastLetter;
    }

    public function filterNoSolo(Positioned $entry): bool
    {
        return !$this->filterSolo($entry);
    }

    public function filterEndingTag(Positioned $entry): bool
    {
        $word = $entry->getInner();
        return '/' == $word[0];
    }

    protected function filteredSoloIntoRecord(Positioned $entry): Record
    {
        $parts = Support::modulePathFromTemplate(Support::clearModuleName($entry->getInner()));
        $rec = new Record();
        $rec->setModuleName(strval(reset($parts)));
        $rec->setModulePath($parts);
        $rec->setContentToChange($entry->getBraced());
        $this->foundModules[$this->makeHash($rec)] = $rec;
        return $rec;
    }

    protected function makeHash(Record $record): string
    {
        return md5(sprintf('%s-%s', implode('::', $record->getModulePath()), $record->getContent()));
    }

    /**
     * @return Record[]
     */
    public function getFoundModules(): array
    {
        return array_values($this->foundModules);
    }
}
