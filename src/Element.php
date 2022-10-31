<?php

namespace Monolyth\Formulaic;

use Stringable;

abstract class Element implements Labelable, Testable, Bindable, Stringable
{
    use Element\Identify;
    use Element\Wrap;
    use Attributes;
    use Validate\Test;
    use Validate\Required;
    use Validate\Element;
    use Transform;
    use Normalize;

    private bool $userInput = false;

    protected ?string $idPrefix = null;

    protected mixed $value = null;

    /**
     * Constructor.
     *
     * @param string $name The element's name.
     */
    public function __construct(string $name)
    {
        $this->attributes['name'] = $name;
        $this->attributes['value'] =& $this->value;
    }

    /**
     * Sets the prefix for the ID attribute (for named forms).
     *
     * @param string|null $prefix
     */
    public function setIdPrefix(string $prefix = null)
    {
        $this->idPrefix = $prefix;
    }

    /**
     * Sets the current value of this element.
     *
     * @param ?string $value The new value.
     * @return self
     */
    public function setValue(?string $value = null) : self
    {
        $this->value = $value;
        if (isset($this->model)) {
            $this->model->{$this->attributes['name']} = $this->transform($value);
        }
        return $this;
    }

    /**
     * Sets the current value of this element, but only if not yet supplied.
     *
     * @param mixed $value The new (default) value.
     * @return self
     */
    public function setDefaultValue($value)
    {
        if (!$this->userInput) {
            $this->setValue($value);
        }
        return $this;
    }

    /**
     * Gets or sets the origin of the current value (user input or bound).
     * Normally, you won't need to call this directly since Formulaic handles
     * data binding transparently.
     *
     * @param bool $status Mark whether or not this value was supplied from an
     *  external source. Omit to just query the current status.
     * @return bool The current status (true for user input, false for
     *              undefined or bound from a model object).
     */
    public function valueSuppliedByUser(bool $status = null) : bool
    {
        if (isset($status)) {
            $this->userInput = (bool)$status;
        }
        return $this->userInput;
    }

    /**
     * Gets a reference to the current value.
     *
     * @return mixed The value.
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * This is here to avoid the need to check instanceof Label.
     *
     * @return self
     */
    public function getElement() : self
    {
        return $this;
    }

    /**
     * Sets the elements disabled state. Note that this doesn't necessarily
     * make sense for all elements, always (e.g. type="hidden").
     *
     * @param boolean $state True for disabled, false for enabled.
     * @return self
     */
    public function disabled(bool $state = true) : self
    {
        $this->attributes['disabled'] = $state;
        return $this;
    }

    /**
     * Sets the placeholder text. Note that this doesn't necessarily make sense
     * for all elements (e.g. type="radio").
     *
     * @param string $text The placeholder text.
     * @return self
     */
    public function placeholder(string $text) : self
    {
        $this->attributes['placeholder'] = $text;
        return $this;
    }

    /**
     * Sets the tabindex. Note that the element can't know if the supplied value
     * makes sense (e.g. is unique in the form), that's up to you.
     *
     * @param int $tabindex The tabindex to use.
     * @return self
     */
    public function tabindex(int $tabindex) : self
    {
        $this->attributes['tabindex'] = (int)$tabindex;
        return $this;
    }

    /**
     * The field must equal the value supplied.
     *
     * @param mixed $test
     * @return self
     */
    public function isEqualTo(mixed $test) : self
    {
        return $this->addTest('equals', fn ($value) => $value == $test);
    }

    /**
    * The field must NOT equal the value supplied.
    *
    * @param mixed $test
    * @return self
    */
    public function isNotEqualTo(mixed $test) : self
    {
        return $this->addTest('differs', fn ($value) => $value != $test);
    }

    /**
     * Bind this element to a model.
     *
     * @param object $model
     * @return self
     */
    public function bind(object $model) : self
    {
        if ($this instanceof Button) {
            return $this;
        }
        $this->model = $model;
        $name = self::normalize($this->name());
        if ($this->valueSuppliedByUser()) {
            $value = $this instanceof Radio ? $this->checked() : $this->getValue();
            try {
                $model->$name = $this->transform($value);
            } catch (TypeError $e) {
                throw new TransformerRequiredException($model, $name, $value);
            }
        } else {
            $this->setValue($this->transform($model->$name ?? null));
        }
        return $this;
    }

    /**
     * Returns a rendered string representation of the element.
     *
     * @return string
     */
    public function __toString() : string
    {
        $work = clone $this;
        $work->generateId();
        $work->generatePrintableName();
    //    $old = $work->prepareToString();
        $out = $work->htmlBefore ?? '';
        $out .= '<input'.$work->attributes().">\n";
        $out .= $work->htmlAfter ?? '';
      //  if (isset($old)) {
        //    $work->attributes['name'] = $old;
        //}
        return $out;
    }

}

