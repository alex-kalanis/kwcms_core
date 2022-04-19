<?php

namespace kalanis\kw_forms;


use kalanis\kw_forms\Adapters\AAdapter;
use kalanis\kw_forms\Adapters\FilesAdapter;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_rules\Validate;
use kalanis\kw_templates\Interfaces\IHtmlElement;
use kalanis\kw_templates\HtmlElement\THtmlElement;


/**
 * Class Form
 * @package kalanis\kw_forms
 * Basic class for work with forms
 */
class Form implements IHtmlElement
{
    use Cache\TStorage;
    use Controls\TSubControls;
    use Controls\TSubErrors;
    use Form\TControl;
    use Form\TMethod;
    use THtmlElement;
    use Controls\TWrappers;

    /** @var Controls\Factory */
    protected $controlFactory = null;
    /** @var Validate */
    protected $validate = null;
    /** @var AAdapter|array */
    protected $entries = [];
    /** @var FilesAdapter|array */
    protected $files = [];
    /** @var string Form label */
    protected $label = '';

    /**
     * Main Form template
     * @var string
     * params: %1 attributes, %2 errors, %3 controls
     */

    /** @var string Template for error output */
    protected $templateErrors = '<div class="errors">%s</div>';
    /**
     * Start tag template - for rendering just inside something
     * @var string
     * params: %1 attributes
     */
    protected $templateStart = '<form %1$s>';
    /**
     * End tag template
     * @var string
     */
    protected $templateEnd = '</form>';

    /**
     * @var string
     * params: %1 labelText, %2 content
     */
    protected $templateLabel = '<fieldset><legend>%1$s</legend>%2$s</fieldset>';

    public function __construct(string $alias = '', ?IHtmlElement $parent = null)
    {
        $this->alias = strval($alias);
        $this->setAttribute('name', $this->alias);
        $this->setMethod(IEntry::SOURCE_POST);
        $this->setTemplate('%2$s<form %1$s>%3$s</form>');

        $this->templateError = '';
        $this->controlFactory = new Controls\Factory();
        $this->validate = new Validate();
        $this->setParent($parent);
    }

    /**
     * @param AAdapter $entries
     * @param FilesAdapter|null $files
     * @return $this
     * @throws Exceptions\FormsException
     */
    public function setInputs(AAdapter $entries, ?FilesAdapter $files = null): self
    {
        $entries->loadEntries($this->getMethod());
        if ($files) {
            $files->loadEntries($this->getMethod());
        }
        $this->entries = $entries;
        $this->files = $files;
        return $this;
    }

    public function getControlFactory(): Controls\Factory
    {
        return $this->controlFactory;
    }

    public function addControlDefaultKey(Controls\AControl $control): void
    {
        $this->addControl($control->getKey(), $control);
    }

    /**
     * Merge children, attr etc.
     * @param IHtmlElement $child
     * @codeCoverageIgnore merge with what?
     */
    public function merge(IHtmlElement $child): void
    {
        if (method_exists($child, 'getLabel')) {
            $this->setLabel($child->getLabel());
        }
        $this->setChildren($child->getChildren());
        $this->setAttributes($child->getAttributes());
    }

    /**
     * Set value of object or child
     * @param string $key
     * @param mixed $value
     */
    public function setValue(string $key, $value = null): void
    {
        $control = $this->getControl($key);
        if ($control) {
            $control->setValue($value);
        }
    }

    /**
     * Get value of object or child
     * @param string $key
     * @return string|string[]|null
     */
    public function getValue(string $key)
    {
        $control = $this->getControl($key);
        return $control ? $control->getValue() : null ;
    }

    /**
     * Get object or child label
     * @param string $key
     * @return string|null
     */
    public function getLabel(?string $key = null)
    {
        if (is_null($key)) {
            return $this->label;
        } else {
            $control = $this->getControl($key);
            if ($control) {
                return $control->getLabel();
            }
        }
        return null;
    }

    /**
     * Set object or child label
     * @param string $value
     * @param string $key
     * @return $this
     */
    public function setLabel(?string $value = null, ?string $key = null)
    {
        if (is_null($key)) {
            $this->label = $value;
        } else {
            $control = $this->getControl($key);
            if ($control) {
                $control->setLabel($value);
            }
        }
        return $this;
    }

    /**
     * Set sent values and process checks on form
     * @param string|null $checkKey
     * @return boolean
     */
    public function process(?string $checkKey = null): bool
    {
        $this->setSentValues();
        if ($this->isSubmitted($checkKey)) {
            return $this->isValid();
        } else {
            return false;
        }
    }

    public function isSubmitted(?string $checkKey = null): bool
    {
        if (empty($this->entries)) {
            return false;
        }

        // any predefined submitted key
        if ($checkKey && $this->entries->offsetExists($checkKey)) {
            return true;
        }

        // lookup for submit button
        foreach ($this->controls as &$control) {
            if ($control instanceof Controls\Submit && !empty($control->getValue())) {
                return true;
            }
        }
        return false;
    }

    /**
     * Set files first, then entries
     * It's necessary due setting checkboxes - files removes that setting, then normal entries set it back
     */
    public function setSentValues(): void
    {
        if ($this->files) $this->setValues($this->setValuesToFill($this->files, true));
        if ($this->entries) $this->setValues($this->setValuesToFill($this->entries));
    }

    protected function setValuesToFill(AAdapter $adapter, bool $raw = false): array
    {
        $result = [];
        foreach ($adapter as $key => $entry) {
            $result[$key] = is_object($entry) && !$raw
                ? ( method_exists($entry, 'getValue')
                    ? $entry->getValue()
                    : strval($entry)
                )
                : $entry ;
        }
        return $result;
    }

    /**
     * Form validation
     * Check each control if is valid
     * @return boolean
     */
    public function isValid(): bool
    {
        $this->errors = [];
        $validation = true;
        foreach ($this->controls as &$child) {
            if ($child instanceof Interfaces\IContainsControls) {
                $validation &= $child->validateControls($this->validate);
                $this->errors += $child->getValidatedErrors();
            } elseif ($child instanceof Controls\AControl) {
                $validation &= $this->validate->validate($child);
                $this->errors += $this->validate->getErrors();
            }
        }

        return $validation;
    }

    public function setTemplate($string): void
    {
        $this->template = $string;
    }

    /**
     * Save current form data in storage
     */
    public function store(): void
    {
        $this->storage->store($this->getValues(), 86400); # day
    }

    /**
     * Load data from storage into form
     */
    public function loadStored(): void
    {
        $this->setValues($this->storage->load());
    }

    /**
     * Render whole form
     * @param string|string[] $attributes
     * @return string
     * @throws Exceptions\RenderException
     */
    public function render($attributes = []): string
    {
        $this->addAttributes($attributes);
        $label = $this->getLabel();
        $content = empty($label) ? $this->renderChildren() : sprintf($this->templateLabel, $label, $this->renderChildren());
        return sprintf($this->template, $this->renderAttributes(), $this->renderErrors(), $content);
    }

    /**
     * Render all errors from controls
     * @return string
     * @throws Exceptions\RenderException
     */
    public function renderErrors(): string
    {
        $errors = $this->renderErrorsArray();
        if (!empty ($errors)) {
            $return = $this->wrapIt(implode('', array_keys($errors)), $this->wrappersErrors);

            return sprintf($this->templateErrors, $return);
        } else {
            return '';
        }
    }

    /**
     * Get all errors from controls and return them as indexed array
     * @return string[]
     * @throws Exceptions\RenderException
     */
    public function renderErrorsArray()
    {
        return $this->getErrors($this->errors, $this->wrappersError);
    }

    /**
     * @param string $key
     * @return string
     * @throws Exceptions\RenderException
     */
    public function renderControlErrors(string $key): string
    {
        $control = $this->getControl($key);
        if (isset($this->errors[$control->getKey()])) {
            return $control->renderErrors($this->errors[$control->getKey()]);
        }
        return '';
    }

    /**
     * Render all form controls, add missing wrappers
     * @return string
     * @throws Exceptions\RenderException
     */
    public function renderChildren(): string
    {
        $return = '';
        $hidden = '';
        foreach ($this->controls as &$child) {

            if ($child instanceof IHtmlElement) {
                if ($child instanceof Controls\AControl) {
                    if (!$child->wrappersLabel()) {
                        $child->addWrapperLabel($this->wrappersLabel);
                    }
                    if (!$child->wrappersInput()) {
                        $child->addWrapperInput($this->wrappersInput);
                    }
                    if (!$child->wrappers()) {
                        $child->addWrapper($this->wrappersChild);
                    }
                }
                if ($child instanceof Controls\Hidden) {
                    $hidden .= $child->render() . PHP_EOL;
                } else {
                    $return .= $child->render() . PHP_EOL;
                }
            } else {
                // @codeCoverageIgnoreStart
                // How to make this one? Only by extending.
                $return .= strval($child);
                // @codeCoverageIgnoreEnd
            }
        }

        return $hidden . $this->wrapIt($return, $this->wrappersChildren);
    }

    /**
     * Set form layout
     * @param string $layoutName
     * @return $this
     */
    public function setLayout(string $layoutName = '')
    {
        if (($layoutName == 'inlineTable') || ($layoutName == 'tableInline')) {
            $this->resetWrappers();
            $this->addWrapperChildren('tr')
                ->addWrapperChildren('table', ['class' => "form"])
                ->addWrapperLabel('td')
                ->addWrapperInput('td')
                ->addWrapperErrors('div', ['class' => "errors"])
                ->addWrapperError('div');
        } elseif ($layoutName == 'table') {
            $this->resetWrappers();
            $this->addWrapperChildren('table', ['class' => "form"])
                ->addWrapperChild('tr')
                ->addWrapperLabel('td')
                ->addWrapperInput('td')
                ->addWrapperErrors('div', ['class' => "errors"])
                ->addWrapperError('div');
        }

        return $this;
    }

    /**
     * Render Start tag and hidden attributes
     * @param array $attributes
     * @param bool $noChildren
     * @return string
     * @throws Exceptions\RenderException
     */
    public function renderStart($attributes = [], bool $noChildren = false): string
    {
        $this->addAttributes($attributes);
        $return = sprintf($this->templateStart, $this->renderAttributes());
        if (!$noChildren) {
            foreach ($this->controls as &$control) {
                if ($control instanceof Controls\Hidden) {
                    $return .= $control->renderInput() . PHP_EOL;
                }
            }
        }

        return $return;
    }

    /**
     * Render End tag
     * @return string
     */
    public function renderEnd(): string
    {
        return $this->templateEnd;
    }
}
