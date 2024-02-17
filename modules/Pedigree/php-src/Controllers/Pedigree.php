<?php

namespace KWCMS\modules\Pedigree\Controllers;


use kalanis\kw_confs\ConfException;
use kalanis\kw_confs\Config;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_mapper\MapperException;
use kalanis\kw_mapper\Records\ARecord;
use kalanis\kw_modules\Interfaces\Lists\ISitePart;
use kalanis\kw_modules\ModuleException;
use kalanis\kw_modules\Output;
use kalanis\kw_paths\Stuff;
use kalanis\kw_pedigree\GetEntries;
use kalanis\kw_pedigree\PedigreeException;
use kalanis\kw_pedigree\Storage;
use kalanis\kw_routed_paths\StoreRouted;
use kalanis\kw_templates\HtmlElement;
use kalanis\kw_templates\TemplateException;
use KWCMS\modules\Core\Libs\AModule;
use KWCMS\modules\Core\Libs\ExternalLink;
use KWCMS\modules\Layout\Controllers\Layout;
use KWCMS\modules\Pedigree\Lib;


/**
 * Class Pedigree
 * @package KWCMS\modules\Pedigree
 * Site's Pedigree - render on page
 */
class Pedigree extends AModule
{
    use Lib\TCorrectConnect;

    const BRANCHES = 2; # count of sub-branches of each entry

    /** @var ExternalLink|null */
    protected $externalLink = null;
    /** @var MapperException|null */
    protected $error = null;
    /** @var GetEntries|null */
    protected $entries = null;
    /** @var int */
    protected $depth = 0;
    /** @var mixed */
    protected $constructParams = [];

    /**
     * @param mixed ...$constructParams
     * @throws ConfException
     * @throws LangException
     * @throws PedigreeException
     */
    public function __construct(...$constructParams)
    {
        Config::load(static::getClassName(static::class));
        $this->externalLink = new ExternalLink(StoreRouted::getPath());
        $this->constructParams = $constructParams;
        \kalanis\kw_pedigree\Config::init();
        $this->initTCorrectConnect($constructParams);
        Lang::load(static::getClassName(static::class));
    }

    public function process(): void
    {
    }

    /**
     * @throws ConfException
     * @throws MapperException
     * @throws ModuleException
     * @throws PedigreeException
     * @throws TemplateException
     * @return Output\AOutput
     */
    public function output(): Output\AOutput
    {
        return ($this->params[ISitePart::KEY_LEVEL] == ISitePart::SITE_CONTENT)
            ? $this->outContent()
            : $this->outLayout($this->outTemplate())
        ;
    }

    protected function outContent(): Output\AOutput
    {
        $out = new Output\Html();
        $tmplLink = new Lib\ExtLinkTemplate();
        $tmplLink->setData(
            $this->createLink($this->getShort()),
            $this->externalLink->linkVariant('pedigree/ped.png','sysimage', true)
        );
        return $out->setContent($tmplLink->render());
    }

    /**
     * @throws MapperException
     * @throws PedigreeException
     * @throws TemplateException
     * @return Output\AOutput
     */
    protected function outTemplate(): Output\AOutput
    {
        try {
            $this->depth = $this->limitedDepth();
            $this->entries = new GetEntries($this->getConnectRecord());
            $tree = $this->getTree($this->getShort());
        } catch (MapperException | PedigreeException $ex) {
            $this->error = $ex;
        }

        $out = new Output\Html();
        if ($this->error) {
            return $out->setContent($this->error->getMessage());
//            return $out->setContent($this->error->getMessage() . nl2br($this->error->getTraceAsString()));
        } else {
            $table = HtmlElement::init('table', ['border' => '1', 'id' => 'pedigree', 'class' => 'pedigree_page']);
            $row1 = HtmlElement::init('tr');
            if (empty($tree[0])) {
                $td = HtmlElement::init('td');
                $td->addChild(Lang::get('pedigree.not_in_db'));
                $row1->addChild($td);
            } else {
                $row1->addChild($this->printTable($tree, $this->getDescendants($tree[0])));
            }
            $table->addChild($row1);
            return $out->setContent($table->render());
        }
    }

    protected function getShort(): string
    {
        $path = StoreRouted::getPath()->getPath();
        $possibleKey = strval(end($path));
        if (!empty($possibleKey) && ('pedigree' != $possibleKey)) {
            return Stuff::fileBase($possibleKey);
        }
        return $this->getFromParam('key');
    }

    /**
     * @param Output\AOutput $output
     * @throws ConfException
     * @throws ModuleException
     * @return Output\AOutput
     */
    protected function outLayout(Output\AOutput $output): Output\AOutput
    {
        $out = new Layout(...$this->constructParams);
        $out->init($this->inputs, $this->params);
        return $out->wrapped($output, false);
    }

    protected function limitedDepth(): int
    {
        $default = intval(Config::get('Pedigree', 'lvl'));
        $level = intval(strval($this->getFromParam('depth', $default)));
        return (intval(Config::get('Pedigree', 'min')) <= $level)
        && (intval(Config::get('Pedigree', 'max')) >= $level)
            ? $level : $default;
    }

    /**
     * @param string $shortKey
     * I DO NOT want to know how I wrote it...
     * @throws PedigreeException
     * @throws MapperException
     * @return array<int, Storage\AEntryAdapter|null>
     */
    protected function getTree(string $shortKey): array
    {
        # read database and fill data
        if (empty($shortKey)) {
            return [];
        }

        $record = $this->entries->getByKey($shortKey);
        if (empty($record)) {
            return [];
        }

        $id = $record->getId();
        if (empty($id)) {
            return [];
        }

        $tree = [];
        $depth = 0; # for getting depth of path
        $used = array_fill(0, $this->depth + 1, 0); # test for already read data - depth - used branches
        $previous = array_fill(0, $this->depth + 1, null); # which one is previous - depth - previous item on position
        $all = $this->countCells($this->countLines(), $this->depth);
        for ($i = 0; $i < $all; $i++) {
            $tree[$i] = $this->readData($id);
            if ($depth < $this->depth) {
                $previous[$depth] = $i;
                $used[$depth]++;
                $depth++;
                $id = $this->upperOrLower($tree[$i], $used[$depth]);
            } else { // when it's too much entries
                do {
                    $depth--;
                } while ($depth > 0 && $used[$depth] >= static::BRANCHES);
                $id = $this->upperOrLower($tree[$previous[$depth]], $used[$depth]);
                $used[$depth]++;
                $depth++;
                for ($j = ($depth); $j <= $this->depth; $j++) { // clear the rest in the branches
                    $used[$j] = 0;
                    $previous[$j] = null;
                }
            }
        }
        return $tree;
    }

    /**
     * @param Storage\AEntryAdapter|null $entry
     * @param int $depth
     * @throws PedigreeException
     * @return int|null
     */
    protected function upperOrLower(?Storage\AEntryAdapter $entry, int $depth): ?int
    {
        if (empty($entry)) {
            return null;
        }
        return (0 == $depth % 2) ? $entry->getFatherId() : $entry->getMotherId();
    }

    /**
     * @param Storage\AEntryAdapter|null $entry
     * @throws MapperException
     * @throws PedigreeException
     * @return ARecord[]
     */
    protected function getDescendants(?Storage\AEntryAdapter $entry): array
    {
        if (empty($entry)) {
            return [];
        }
        return $entry->getChildren();
    }

    /**
     * @param string|null $id
     * @throws MapperException
     * @throws PedigreeException
     * @return Storage\AEntryAdapter|null
     */
    protected function readData(?string $id): ?Storage\AEntryAdapter
    {
        # read database and fill data
        if (empty($id)) {
            return null;
        }
        $record = $this->entries->getById($id);
        if (empty($record->getName())) {
            return null;
        }
        return $record;
    }

    /**
     * @param array<int, Storage\AEntryAdapter|null> $tree
     * @param Storage\AEntryAdapter[] $descendants
     * @throws PedigreeException
     * @throws TemplateException
     * @return string
     */
    protected function printTable(array $tree, array $descendants): string
    {
        $posFromBeginning = 1;
        $content = $this->printCell($tree, $this->countLines(), 0, 0);
        for ($i = 0; $i < $this->countLines(); $i++) { // lines
            $cells = $this->printCellsOnLine($i, $this->depth);
            $inner = $this->depth - ($cells - 1);
            for ($j = 0; $j < $cells; $j++) { // cols - and cells
                $n = pow(static::BRANCHES, $cells - ($j + 1)); // how many rows will be there
                $content .= $this->printCell($tree, $n, $inner + $j, $posFromBeginning);
                $posFromBeginning++;
            }
            $content .= '</tr><tr>';
        }
        $content = str_replace('{DESCENDANTS_LIST}', $this->printDescendants($descendants), $content);
        return $content;
    }

    /**
     * @param Storage\APedigreeRecord[] $descendants
     * @throws PedigreeException
     * @return string
     */
    protected function printDescendants(array $descendants): string
    {
        $tmpl = new Lib\DescLinkTemplate();
        $r = [];
        foreach ($descendants as &$descendant) {
            $lib = clone $this->entries->getStorage();
            $lib->setRecord($descendant);
            $r[] = $tmpl->reset()->setData(
                $this->createLink($lib->getShort()),
                $lib->getName(),
                $lib->getFamily()
            )->render();
        }
        return implode('', $r);
    }

    /**
     * @param array<int, Storage\AEntryAdapter|null> $tree
     * @param int $span
     * @param int $cnt
     * @param int $position
     * @throws TemplateException
     * @return string
     */
    protected function printCell(array $tree, int $span, int $cnt, int $position): string
    {
        # return content of actual cell
        $libCell = new Lib\CellTemplate();
        if ($position == 0) { // first column
            $libCell->setTemplateName('first');
        } elseif ($cnt <= Config::get('Pedigree', 'sub')) { // extended info until selected level
            $libCell->setTemplateName('ext');
        } else { // test of cells
            $libCell->setTemplateName('norm');
        }
        $libCell->reset();
        if (empty($tree[$position])) {
            $libCell->setTemplateName('no_info');
            $libCell->reset();
            $libCell->setData($span);
        } else {
            $storage = $tree[$position];
            $libCell->setData(
                strval($span),
                $storage->getName(),
                $storage->getFamily(),
                $this->createLink($storage->getShort()),
                $storage->getSuccesses()
            );
        }
        return $libCell->render();
    }

    protected function printCellsOnLine(int $line, int $total): int
    {
        # print count of cells on line
        if ($line == 0) {
            return $total;
        } else {
            for ($b = 1; $b < $this->depth; $b++) {
                for ($x = 0; $x < ($this->countLines() / static::BRANCHES); $x++) {
                    $cnt1 = pow(static::BRANCHES, $b); // p^b
                    $cnt2 = pow(static::BRANCHES, ($b - 1)); //  p^(b-1)
                    $cnt = ($x * $cnt1) + $cnt2;
                    $dv = $cnt % $line;
                    if ($dv === 0) {
                        return $b;
                    }
                }
            }
            return $total;
        }
    }

    protected function createLink(string $key): string
    {
        return $this->externalLink->linkVariant('pedigree/' . $key, 'pedigree');
    }

    protected function countCells(int $actual, int $left): int
    {
        # return count of all cells - recursion
        return ($left > 0) ? ($actual + $this->countCells(intval($actual / static::BRANCHES), $left - 1)) : $actual;
    }

    protected function countLines(): int
    {
        # print total count of lines
        return (int)pow(static::BRANCHES, $this->depth);
    }
}
