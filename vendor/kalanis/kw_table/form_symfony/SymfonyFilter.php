<?php

namespace kalanis\kw_table\form_symfony;


use kalanis\kw_table\core\Interfaces\Form\IField;
use kalanis\kw_table\core\Interfaces\Form\IFilterForm;
use kalanis\kw_table\core\TableException;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;


/**
 * Class SymfonyFilter
 * @package kalanis\kw_table\form_symfony
 * Use Symfony framework and form as source of params
 */
class SymfonyFilter implements IFilterForm
{
    protected $applyName = '';
    /** @var FormBuilderInterface */
    protected $form;
    /** @var bool */
    protected $formProcess = false;
    /** @var string[] */
    protected $formData = [];

    public function __construct(FormBuilderInterface $form)
    {
        $this->applyName = 'apply' . ucfirst($form->getName());
        $form->setMethod('get');
        $form->add($this->applyName, HiddenType::class);
        $apply = $form->get($this->applyName);
        $apply->setData('apply');
        $this->form = $form;
    }

    public function addField(IField $field): void
    {
        if (!$field instanceof Fields\AField) {
            throw new TableException('Not an instance of \kalanis\kw_table\form_symfony\Fields\AField.');
        }

        $field->setForm($this->form);
        $field->add();
    }

    public function setValue(string $alias, $value): void
    {
        $this->form->get($alias)->setData($value);
    }

    public function getValues(): array
    {
        $this->process();
        return $this->formData;
    }

    public function getValue(string $alias)
    {
        if ($this->process()) {
            return $this->formData[$alias];
        }

        return null;
    }

    public function getFormName(): string
    {
        return $this->form->getAttribute('name');
    }

    public function renderStart(): string
    {
        return $this->form->renderStart();
    }

    public function renderEnd(): string
    {
        return $this->form->renderEnd();
    }

    public function renderField(string $alias): string
    {
        try {
            $control = $this->form->get($alias);
            return $control->get();
        } catch (InvalidArgumentException $ex) {
            return '';
        }

    }

    protected function process(): bool
    {
        if ($this->formProcess) {
            return $this->formProcess;
        }

        $this->formProcess = $this->form->process('apply' . ucfirst($this->form->getAlias()));
        $this->formData = $this->form->getValues();
        return $this->formProcess;
    }
}
