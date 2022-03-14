<?php

namespace KWCMS\modules\Menu\Forms;


use kalanis\kw_forms\Adapters\AAdapter;
use kalanis\kw_forms\Controls;
use kalanis\kw_forms\Form;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_langs\Lang;
use kalanis\kw_menu\Menu\Entry;


/**
 * Class EditPosForm
 * @package KWCMS\modules\Menu\Forms
 * Edit positions in menu
 * @property Controls\Submit saveFile
 * @property Controls\Reset resetFile
 * Contains extra functions because it's necessary to pass data as 2-dimensional array and fill them as 1-dimensional
 */
class EditPosForm extends Form
{
    protected $inputs = [];
    protected $filledValues = [];

    public function composeForm(array $items, int $displayCounter): self
    {
        $this->setMethod(IEntry::SOURCE_POST);
        array_walk($items, [$this, 'addItemControl']);
        $this->addHidden('display', $displayCounter , ['id' => 'display_count']);
        $this->addSubmit('saveFile', Lang::get('dashboard.button_set'));
        $this->addReset('resetFile', Lang::get('dashboard.button_reset'));
        return $this;
    }

    protected function addItemControl(Entry $item, $key): void
    {
        $input = new InputPosition();
        $input->set(sprintf('pos[%s]', $key), $item->getPosition(), $item->getName());
        $this->addControl($input);
        $this->inputs[] = $input;
    }

    public function getInputs(): array
    {
        return $this->inputs;
    }

    protected function setValuesToFill(AAdapter $adapter, bool $raw = false): array
    {
        $result = [];
        $this->filledValues = [];
        foreach ($adapter as $key => $entry) {
            $value = is_object($entry) && !$raw
                ? ( method_exists($entry, 'getValue')
                    ? $entry->getValue()
                    : strval($entry)
                )
                : $entry
            ;
            $result[$key] = is_array($value) && !$raw
                ? $this->addArrayFill($result, $value, $key)
                : $value
            ;
        }
        return $result;
    }

    protected function addArrayFill(&$result, array $entry, $key)
    {
        foreach ($entry as $subKey => $item) {
            $usingKey = sprintf('%s[%s]', $key, $subKey);
            $result[$usingKey] = $item;
            $this->filledValues[$subKey] = $item;
        }
        return $entry;
    }

    /**
     * @return array<string, int>
     */
    public function getPositions(): array
    {
        return $this->filledValues;
    }
}
