<?php

namespace kalanis\kw_forms\Adapters;


use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_input\Interfaces\IEntry;


class VarsAdapter extends AAdapter
{
    protected $inputType = '';

    public function loadEntries(string $inputType): void
    {
        if (IEntry::SOURCE_POST == $inputType) {
            $this->vars = $this->loadVars($_POST);
        } elseif (IEntry::SOURCE_GET == $inputType) {
            $this->vars = $this->loadVars($_GET);
        } else {
            throw new FormsException(sprintf('Unknown input type - %s', $inputType));
        }
        $this->inputType = $inputType;
    }

    protected function loadVars(&$array): array
    {
        $result = [];
        if (is_array($array)) {
            foreach ($array as $postedKey => $posted) {
                $result[$this->removeNullBytes($postedKey)] = $this->removeNullBytes($posted);
            }
        }
        return $result;
    }

    public function getSource(): string
    {
        return $this->inputType;
    }
}
