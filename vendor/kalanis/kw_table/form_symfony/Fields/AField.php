<?php

namespace kalanis\kw_table\form_symfony\Fields;


use kalanis\kw_connect\core\Interfaces\IIterableConnector;
use kalanis\kw_table\core\Interfaces\Form\IField;
use Symfony\Component\Form\FormBuilderInterface;


/**
 * Class AField
 * @package kalanis\kw_table\form_symfony\Fields
 */
abstract class AField implements IField
{
    /** @var FormBuilderInterface|null */
    protected $form = null;
    /** @var string */
    protected $alias = '';
    /** @var string[]|int[] */
    protected $attributes = [];
    /** @var IIterableConnector */
    protected $connector = null;

    /**
     * @param string[] $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->setAttributes($attributes);
    }

    public function setForm(FormBuilderInterface $form): void
    {
        $this->form = $form;
    }

    public function getForm(): ?FormBuilderInterface
    {
        return $this->form;
    }

    public function setAlias(string $alias): void
    {
        $this->alias = $alias;
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function setDataSourceConnector(IIterableConnector $dataSource): void
    {
        $this->connector = $dataSource;
    }

    public function addAttribute(string $name, string $value): void
    {
        $this->attributes[$name] = $value;
    }

    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes + $this->attributes;
    }
}
