<?php

namespace kalanis\kw_forms\Adapters;


use kalanis\kw_forms\Exceptions\FormsException;


class FilesAdapter extends AAdapter
{
    public function loadEntries(string $inputType): void
    {
        $this->vars = $this->loadVars($_FILES);
    }

    protected function loadVars(&$array): array
    {
        $entry = new FileEntry();
        $result = [];
        foreach ($array as $postedKey => $posted) {
            if (is_array($posted['name'])) {
                foreach ($posted['name'] as $key => $value) {
                    $data = clone $entry;
                    $data->setData(
                        sprintf('%s[%s]', $this->removeNullBytes($postedKey), $this->removeNullBytes($key)),
                        $this->removeNullBytes($value),
                        $posted['tmp_name'][$key],
                        $posted['type'][$key],
                        intval($posted['error'][$key]),
                        intval($posted['size'][$key])
                    );
                    $result[$data->getKey()] = $data;
                }
            } else {
                $data = clone $entry;
                $data->setData(
                    $this->removeNullBytes($postedKey),
                    $this->removeNullBytes($posted['name']),
                    $posted['tmp_name'],
                    $posted['type'],
                    intval($posted['error']),
                    intval($posted['size'])
                );
                $result[$data->getKey()] = $data;
            }
        }
        return $result;
    }

    public function current()
    {
        if ($this->valid()) {
            return $this->offsetGet($this->key);
        }
        throw new FormsException(sprintf('Unknown offset %s', $this->key));
    }

    public function getSource(): string
    {
        return static::SOURCE_FILES;
    }
}
