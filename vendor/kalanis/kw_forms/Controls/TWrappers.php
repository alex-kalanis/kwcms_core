<?php

namespace kalanis\kw_forms\Controls;


use kalanis\kw_forms\Exceptions\RenderException;
use kalanis\kw_templates\HtmlElement;


/**
 * Wrapper trait for form inputs
 * @author Petr Plsek
 * @author Adam Dornak
 * @author Petr Bolehovsky
 */
trait TWrappers
{
    use TTemplateError;

    /** @var HtmlElement\IHtmlElement[] */
    protected $wrappers = [];
    /** @var HtmlElement\IHtmlElement[] */
    protected $wrappersLabel = [];
    /** @var HtmlElement\IHtmlElement[] */
    protected $wrappersInput = [];
    /** @var HtmlElement\IHtmlElement[] */
    protected $wrappersChild = [];
    /** @var HtmlElement\IHtmlElement[] */
    protected $wrappersChildren = [];
    /** @var HtmlElement\IHtmlElement[] */
    protected $wrappersError = [];
    /** @var HtmlElement\IHtmlElement[] */
    protected $wrappersErrors = [];

    /**
     * Pack string into preset html element
     * @param string $string
     * @param HtmlElement\IHtmlElement|HtmlElement\IHtmlElement[] $wrappers
     * @return string
     * @throws RenderException
     */
    protected function wrapIt(string $string, $wrappers): string
    {
        $return = $string;
        if (is_array($wrappers)) {
            foreach ($wrappers as $wrapper) {
                $return = $this->wrapIt($return, $wrapper);
            }
        } elseif ($wrappers instanceof HtmlElement\IHtmlElement) {
            $wrappers->addChild($return);
            $return = $wrappers->render();
        } else {
            throw new RenderException('Wrapper must be an instance of IHtmlElement or array of its instances');
        }

        return $return;
    }

    /**
     * Add wrapper into predefined stack
     * @param HtmlElement\IHtmlElement[] $stack
     * @param HtmlElement\IHtmlElement|HtmlElement\IHtmlElement[]|string|string[] $wrapper
     * @param mixed $attributes
     */
    protected function addWrapperToStack(&$stack, $wrapper, array $attributes = []): void
    {
        if (is_array($wrapper)) {
            foreach ($wrapper as $_wrapper) {
                $this->addWrapperToStack($stack, $_wrapper);
            }
        } else {
            if (!($wrapper instanceof HtmlElement\IHtmlElement)) {
                $wrapper = HtmlElement::init($wrapper, $attributes);
            } else if ($attributes !== null) {
                $wrapper->setAttributes($attributes);
            }
            if (!in_array($wrapper, $stack)) {
                $stack[] = $wrapper;
            }
        }
    }

    /**
     * Add wrapper for the whole object
     * @param string|string[]|HtmlElement\IHtmlElement|HtmlElement\IHtmlElement[] $wrapper
     * @param mixed $attributes
     * @return $this
     * @see AControl::render
     */
    public function addWrapper($wrapper, array $attributes = []): self
    {
        $this->addWrapperToStack($this->wrappers, $wrapper, $attributes);
        return $this;
    }

    /**
     * Add wrapper for each child
     * @param string|string[]|HtmlElement\IHtmlElement|HtmlElement\IHtmlElement[] $wrapper
     * @param mixed $attributes
     * @return $this
     * @see AControl::renderChild
     */
    public function addWrapperChild($wrapper, array $attributes = []): self
    {
        $this->addWrapperToStack($this->wrappersChild, $wrapper, $attributes);
        return $this;
    }

    /**
     * Add wrapper for labels
     * @param string|string[]|HtmlElement\IHtmlElement|HtmlElement\IHtmlElement[] $wrapper
     * @param mixed $attributes
     * @return $this
     * @see AControl::renderLabel
     */
    public function addWrapperLabel($wrapper, array $attributes = []): self
    {
        $this->addWrapperToStack($this->wrappersLabel, $wrapper, $attributes);
        return $this;
    }

    /**
     * Add wrapper for inputs
     * @param string|string[]|HtmlElement\IHtmlElement|HtmlElement\IHtmlElement[] $wrapper
     * @param mixed $attributes
     * @return $this
     * @see AControl::renderInput
     */
    public function addWrapperInput($wrapper, array $attributes = []): self
    {
        $this->addWrapperToStack($this->wrappersInput, $wrapper, $attributes);
        return $this;
    }

    /**
     * Add wrapper for content of children
     * @param string|string[]|HtmlElement\IHtmlElement|HtmlElement\IHtmlElement[] $wrapper
     * @param mixed $attributes
     * @return $this
     * @see AControl::renderChildren
     */
    public function addWrapperChildren($wrapper, array $attributes = []): self
    {
        $this->addWrapperToStack($this->wrappersChildren, $wrapper, $attributes);
        return $this;
    }

    /**
     * Add wrapper for error messages
     * @param string|string[]|HtmlElement\IHtmlElement|HtmlElement\IHtmlElement[] $wrapper
     * @param string|array $attributes
     * @return $this
     * @see AControl::renderErrors
     */
    public function addWrapperError($wrapper, array $attributes = []): self
    {
        $this->addWrapperToStack($this->wrappersError, $wrapper, $attributes);
        return $this;
    }

    /**
     * Add wrapper for error messages
     * @param string|string[]|HtmlElement\IHtmlElement|HtmlElement\IHtmlElement[] $wrapper
     * @param string|array $attributes
     * @return $this
     * @see AControl::renderErrors
     */
    public function addWrapperErrors($wrapper, array $attributes = []): self
    {
        $this->addWrapperToStack($this->wrappersErrors, $wrapper, $attributes);
        return $this;
    }

    public function wrappers(): array
    {
        return $this->wrappers;
    }

    public function wrappersLabel(): array
    {
        return $this->wrappersLabel;
    }

    public function wrappersInput(): array
    {
        return $this->wrappersInput;
    }

    public function wrappersChild(): array
    {
        return $this->wrappersChild;
    }

    public function wrappersChildren(): array
    {
        return $this->wrappersChildren;
    }

    public function wrappersError(): array
    {
        return $this->wrappersError;
    }

    public function wrappersErrors(): array
    {
        return $this->wrappersErrors;
    }

    public function resetWrappers(): self
    {
        $this->wrappers = [];
        $this->wrappersLabel = [];
        $this->wrappersInput = [];
        $this->wrappersChild = [];
        $this->wrappersChildren = [];
        $this->wrappersError = [];
        $this->wrappersErrors = [];
        return $this;
    }
}
