<?php

namespace kalanis\kw_clipr\Output;


use kalanis\kw_clipr\Clipr\Useful;


/**
 * Class PrettyTable
 *
 * Lib which makes pretty tables (Markdown rulezz!)
 *
 * Usage (basically twice, once set, then output):
 * <pre>
 *   # set
 *   $libPrettyTable = new PrettyTable();
 *   $libPrettyTable->setHeaders(['tb1','tb2','tb3']);
 *   $libPrettyTable->setColors(['yellow','','blue']);
 *   $libPrettyTable->setDataLine(['abc','def','ghi']);
 *   $libPrettyTable->setDataLine(['rst','uvw','xyz']);
 *   # print
 *   cliprATask->outputLn($libPrettyTable->getHeader());
 *   cliprATask->outputLn($libPrettyTable->getSeparator());
 *   foreach ($libPrettyTable as $row) {
 *       cliprATask->outputLn($row);
 *   }
 * </pre>
 * @package kalanis\kw_clipr\Output
 */
class PrettyTable implements \Iterator
{

    protected $header = [];
    protected $colors = [];
    protected $colorsHeader = [];
    protected $table = [];
    protected $position = 0;
    protected $lengths = [];

    public function setColors($values): void
    {
        $this->colors = $values;
    }

    public function setColor($index, string $value): void
    {
        $this->colors[$index] = $value;
    }

    public function setColorsHeader($values): void
    {
        $this->colorsHeader = $values;
    }

    public function setColorHeader($index, string $value): void
    {
        $this->colorsHeader[$index] = $value;
    }

    public function setHeaders($values): void
    {
        $this->header = $values;
    }

    public function setHeader($index, $value): void
    {
        $this->header[$index] = $value;
    }

    public function setDataLine($values): void
    {
        $this->table[$this->position] = $values;
        $this->next();
    }

    public function setData($index, $value): void
    {
        $this->table[$this->position][$index] = $value;
    }

    public function setLengths($force = false): void
    {
        if (empty($this->lengths) || $force) {
            // for correct padding it's necessary to set max lengths for each column
            $outputArray = array_merge([$this->header], $this->table);
            foreach ($outputArray as $row) {
                foreach ($row as $index => $item) {
                    $len = mb_strlen($item);
                    if (!isset($this->lengths[$index]) || ($this->lengths[$index] < $len)) {
                        $this->lengths[$index] = $len;
                    }
                }
            }
        }
    }

    #[\ReturnTypeWillChange]
    public function current()
    {
        $this->setLengths();
        return $this->dumpLine($this->table[$this->position], $this->colors);
    }

    public function prev(): void
    {
        $this->position--;
    }

    public function next(): void
    {
        $this->position++;
    }

    /**
     * @return int|mixed
     * @codeCoverageIgnore no access inside iterator
     */
    #[\ReturnTypeWillChange]
    public function key()
    {
        // @codeCoverageIgnoreStart
        return $this->position;
    }
    // @codeCoverageIgnoreEnd

    public function valid(): bool
    {
        return isset($this->table[$this->position]);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function getHeader(): string
    {
        $this->setLengths();
        if (!empty($this->header)) {
            return $this->dumpLine($this->header, $this->colorsHeader + $this->colors);
        }
        return '';
    }

    public function getSeparator(): string
    {
        $this->setLengths();
        if (!empty($this->lengths)) {
            $line = array();
            foreach ($this->lengths as $index => $length) {
                $line[$index] = str_repeat('-', $length);
            }
            return $this->dumpLine($line, $this->colorsHeader + $this->colors);
        }
        return '';
    }

    protected function dumpLine($content, $colors = array()): string
    {
        $line = array();
        foreach ($content as $index => $item) {
            $padded = Useful::mb_str_pad($item, $this->lengths[$index]);
            if (empty($colors[$index])) {
                $line[] = $padded;
            } else {
                $color = $colors[$index];
                $line[] = '<' . $color . '>' . $padded . '</' . $color . '>';
            }
        }
        return '| ' . implode(' | ', $line) . ' |';
    }
}
