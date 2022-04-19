<?php

namespace kalanis\kw_forms\Controls;


use kalanis\kw_forms\Exceptions\RenderException;
use kalanis\kw_forms\Interfaces;
use kalanis\kw_templates\Interfaces\IHtmlElement;


/**
 * Trait TSubControls
 * @package kalanis\kw_forms\Controls
 * Trait for rendering other controls
 */
trait TSubControls
{
    /** @var AControl[] */
    protected $controls = [];

    public function addControl(string $key, AControl $control): void
    {
        $this->controls[$key] = $control;
    }

    public function getControl(string $key): ?AControl
    {
        foreach ($this->controls as &$control) {
            if ($control instanceof Interfaces\IContainsControls && $control->hasControl($key)) {
                return $control->getControl($key);
            } elseif ($control instanceof AControl) {
                if ($control->getKey() == $key) {
                    return $control;
                }
            }
        }
        return null;
    }

    /**
     * Get values of all children
     * @return string[]
     */
    public function getValues(): array
    {
        $array = [];
        foreach ($this->controls as $key => &$child) {
            if ($child instanceof Interfaces\IMultiValue) {
                $array += $child->getValues();
            } else {
                $_alias = ($child instanceof AControl) ? $child->getKey() : $key ;
                $array[$_alias] = $child->getValue();
            }
        }
        return $array;
    }

    /**
     * Set values to all children, !!undefined values will NOT be set!!
     * <b>Usage</b>
     * <code>
     *  $form->setValues($this->context->post) // set values from Post
     *  $form->setValues($mapperObject) // set values from other source
     * </code>
     * @param string[] $data
     */
    public function setValues(array $data = []): void
    {
        foreach ($this->controls as $key => &$child) {
            if ($child instanceof Interfaces\IMultiValue) {
                $child->setValues($data);
            } else {
                $_alias = ($child instanceof AControl) ? $child->getKey() : $key ;
                if (isset($data[$_alias])) {
                    $child->setValue($data[$_alias]);
                }
            }
        }
    }

    /**
     * Get labels of all children
     * @return array
     */
    public function getLabels(): array
    {
        $array = [];
        foreach ($this->controls as &$child) {
            if ($child instanceof Interfaces\IContainsControls) {
                $array += $child->getLabels();
            } else {
                $array[$child->getKey()] = $child->getLabel();
            }
        }
        return $array;
    }

    /**
     * Set labels to all children
     * @param string[] $array
     * @return void
     */
    public function setLabels(array $array = []): void
    {
        foreach ($this->controls as &$child) {
            if ($child instanceof Interfaces\IContainsControls) {
                $child->setLabels($array);
            } elseif (isset($array[$child->getKey()])) {
                $child->setLabel($array[$child->getKey()]);
            }
        }
    }

    /**
     * @param string[] $passedErrors
     * @param string|string[]|IHtmlElement|IHtmlElement[] $wrappersError
     * @return array
     * @throws RenderException
     */
    public function getErrors(array $passedErrors, array $wrappersError): array
    {
        $returnErrors = [];
        foreach ($this->controls as &$child) {
            if ($child instanceof Interfaces\IContainsControls) {
                $returnErrors += $child->getErrors($passedErrors, $wrappersError);
            } elseif ($child instanceof AControl) {
                if (isset($passedErrors[$child->getKey()])) {
                    if (!$child->wrappersErrors()) {
                        $child->addWrapperErrors($wrappersError);
                    }
                    $returnErrors[$child->getKey()] = $child->renderErrors($passedErrors[$child->getKey()]);
                }
            }
        }

        return $returnErrors;
    }
}
