<?php

namespace KWCMS\modules\Pedigree;


use kalanis\kw_confs\Config;
use kalanis\kw_langs\Lang;
use kalanis\kw_mapper\MapperException;
use kalanis\kw_mapper\Records\ARecord;
use kalanis\kw_modules\AModule;
use kalanis\kw_modules\ExternalLink;
use kalanis\kw_modules\Interfaces\ILoader;
use kalanis\kw_modules\Interfaces\ISitePart;
use kalanis\kw_modules\Output;
use kalanis\kw_modules\Processing\Modules;
use kalanis\kw_pedigree\GetEntries;
use kalanis\kw_pedigree\PedigreeException;
use kalanis\kw_pedigree\Storage;
use kalanis\kw_templates\HtmlElement;
use KWCMS\modules\Layout\Layout;


/**
 * Class Pedigree
 * @package KWCMS\modules\Pedigree
 * Site's Pedigree - render on page
 */
class Pedigree extends AModule
{
    const BRANCHES = 2; # count of sub-branches of each entry

    /** @var ExternalLink|null */
    protected $externalLink = null;
    /** @var MapperException|null */
    protected $error = null;
    /** @var GetEntries|null */
    protected $entries = null;
    /** @var int */
    protected $depth = 0;
    /** @var ILoader|null */
    protected $loader = null;
    /** @var Modules|null */
    protected $processor = null;

    public function __construct(?ILoader $loader = null, ?Modules $processor = null)
    {
        Config::load(static::getClassName(static::class));
        $this->externalLink = new ExternalLink(Config::getPath());
        $this->loader = $loader;
        $this->processor = $processor;
        Lang::load(static::getClassName(static::class));
    }

    public function process(): void
    {
    }

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
            $this->createLink($this->getFromParam('key')),
            $this->externalLink->linkVariant('pedigree/ped.png','sysimage', true)
        );
        return $out->setContent($tmplLink->render());
    }

    protected function outTemplate(): Output\AOutput
    {
        try {
            $this->depth = $this->limitedDepth();
            $this->entries = new GetEntries($this->getRecord());
            $tree = $this->getTree(strval($this->getFromParam('key')));
        } catch (MapperException | PedigreeException $ex) {
            $this->error = $ex;
        }

        $out = new Output\Html();
        if ($this->error) {
            return $out->setContent($this->error->getMessage());
//            return $out->setContent($this->error->getMessage() . nl2br($this->error->getTraceAsString()));
        } else {
            $table = HtmlElement::init('table', ['border' => '1', 'id' => 'pedigree']);
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

    protected function outLayout(Output\AOutput $output): Output\AOutput
    {
        $out = new Layout($this->loader, $this->processor);
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

    protected function getRecord(): ARecord
    {
        \kalanis\kw_pedigree\Config::init();
        return new Storage\SingleTable\PedigreeRecord();
//        return new Storage\MultiTable\PedigreeItemRecord();
    }

    /**
     * @param string $key
     * @return ARecord[]
     * @throws MapperException
     * I DO NOT want to know how I wrote it...
     */
    protected function getTree(string $key): array
    {
        # read database and fill data
        if (empty($key)) {
            return [];
        }

        $record = $this->entries->getByKey($key);
        $id = strval($record->offsetGet($this->entries->getStorage()->getIdKey()));
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

    protected function upperOrLower(?ARecord $record, int $depth): string
    {
        if (empty($record)) {
            return '';
        }
        $storage = $this->entries->getStorage();
        $storage->setRecord($record);
        return (0 == $depth % 2) ? $storage->getFatherId() : $storage->getMotherId();
    }

    /**
     * @param ARecord|null $record
     * @return ARecord[]
     * @throws MapperException
     */
    protected function getDescendants(?ARecord $record): array
    {
        if (empty($record)) {
            return [];
        }
        $storage = $this->entries->getStorage();
        $storage->setRecord($record);
        $storage->setRecord($this->entries->getById($storage->getId()));
        return $storage->getChildren();
    }

    /**
     * @param string $id
     * @return ARecord|null
     * @throws MapperException
     */
    protected function readData(string $id): ?ARecord
    {
        # read database and fill data
        if (empty($id)) {
            return null;
        }
        $storage = $this->entries->getStorage();
        $record = $this->entries->getById($id);
        if (empty($record->offsetGet($storage->getNameKey()))) {
            return null;
        }
        return $record;
    }

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

    protected function printDescendants(array $descendants): string
    {
        $tmpl = new Lib\DescLinkTemplate();
        $r = [];
        $storage = $this->entries->getStorage();
        foreach ($descendants as &$descendant) {
            $storage->setRecord($descendant);
            $r[] = $tmpl->reset()->setData(
                $this->createLink($storage->getId()),
                $storage->getName(),
                $storage->getFamily()
            )->render();
        }
        return implode('', $r);
    }

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
            $storage = $this->entries->getStorage();
            $storage->setRecord($tree[$position]);
            $libCell->setData(
                (string)$span,
                $storage->getName(),
                $storage->getFamily(),
                $this->createLink($storage->getId()),
                $storage->getTrials()
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
        $link = $this->externalLink->linkVariant('pedigree/pedigree'); // for now, not final one...
        $link .= '?key=' . $key;
        return $link;
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
